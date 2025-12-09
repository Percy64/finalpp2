<?php
/**
 * Pet Model
 */
class Pet {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT m.*, u.nombre as nombre_dueno, u.email as email_dueno, u.telefono as telefono_dueno
            FROM mascotas m
            LEFT JOIN usuarios u ON m.id = u.id
            WHERE m.id_mascota = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT m.*, cq.codigo_qr 
            FROM mascotas m 
            LEFT JOIN codigos_qr cq ON m.id_qr = cq.id_qr 
            WHERE m.id = ? 
            ORDER BY m.fecha_registro DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findLostPets() {
        $sql = "SELECT
                    m.id_mascota,
                    m.nombre,
                    m.especie,
                    m.raza,
                    m.color,
                    m.foto_url,
                    m.estado,
                    m.descripcion_estado,
                    m.fecha_estado,
                    COALESCE(mp.ubicacion, u.direccion) AS ubicacion,
                    mp.lat AS latitud,
                    mp.lng AS longitud,
                    mp.fecha_perdida
                FROM mascotas m
                LEFT JOIN (
                    SELECT mp1.*
                    FROM mascotas_perdidas mp1
                    JOIN (
                        SELECT id_mascota, MAX(id_perdida) AS max_id
                        FROM mascotas_perdidas
                        GROUP BY id_mascota
                    ) last ON last.id_mascota = mp1.id_mascota AND last.max_id = mp1.id_perdida
                ) mp ON mp.id_mascota = m.id_mascota
                LEFT JOIN usuarios u ON m.id = u.id
                WHERE TRIM(m.estado) = 'perdida' OR mp.id_perdida IS NOT NULL
                ORDER BY COALESCE(m.fecha_estado, mp.fecha_perdida) DESC, m.id_mascota DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Fallback sin mascotas_perdidas
            $sqlSimple = "SELECT m.id_mascota, m.nombre, m.especie, m.raza, m.color, m.foto_url, m.estado, 
                                 m.descripcion_estado, m.fecha_estado, u.direccion AS ubicacion, 
                                 NULL AS latitud, NULL AS longitud, NULL AS fecha_perdida
                          FROM mascotas m
                          LEFT JOIN usuarios u ON m.id = u.id
                          WHERE TRIM(m.estado) = 'perdida'
                          ORDER BY m.fecha_estado DESC, m.id_mascota DESC";
            $stmt = $this->db->prepare($sqlSimple);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO mascotas (id, nombre, especie, raza, edad, color, genero, descripcion, foto_url, estado, fecha_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'normal', NOW())
        ");
        $stmt->execute([
            $data['id'],
            $data['nombre'],
            $data['especie'],
            $data['raza'] ?? null,
            $data['edad'] ?? null,
            $data['color'] ?? null,
            $data['genero'] ?? null,
            $data['descripcion'] ?? null,
            $data['foto_url'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id_mascota' && $key !== 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $sql = "UPDATE mascotas SET " . implode(', ', $fields) . " WHERE id_mascota = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id, $userId) {
        // Verificar propiedad
        $stmt = $this->db->prepare("SELECT foto_url, id_qr FROM mascotas WHERE id_mascota = ? AND id = ?");
        $stmt->execute([$id, $userId]);
        $mascota = $stmt->fetch();
        
        if (!$mascota) {
            return false;
        }
        
        // Eliminar archivo físico
        if (!empty($mascota['foto_url'])) {
            $publicPath = ltrim(str_replace('\\', '/', $mascota['foto_url']), '/');
            $fsPath = rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . '/' . $publicPath;
            if (file_exists($fsPath)) {
                @unlink($fsPath);
            }
        }
        
        // Eliminar historial médico
        $stmt = $this->db->prepare("DELETE FROM historial_medico WHERE id_mascota = ?");
        $stmt->execute([$id]);
        
        // Eliminar mascota
        $stmt = $this->db->prepare("DELETE FROM mascotas WHERE id_mascota = ? AND id = ?");
        $result = $stmt->execute([$id, $userId]);
        
        // Eliminar QR
        if (!empty($mascota['id_qr'])) {
            $stmt = $this->db->prepare("DELETE FROM codigos_qr WHERE id_qr = ?");
            $stmt->execute([$mascota['id_qr']]);
        }
        
        return $result;
    }

    public function changeStatus($id, $estado, $descripcion = null, $ubicacion = null, $lat = null, $lng = null, $usuarioId = null) {
        try {
            $this->db->beginTransaction();

            // Actualizar estado principal de la mascota
            $stmt = $this->db->prepare("
                UPDATE mascotas 
                SET estado = ?, descripcion_estado = ?, fecha_estado = NOW() 
                WHERE id_mascota = ?
            ");
            $stmt->execute([$estado, $descripcion, $id]);

            // Registrar ubicación de pérdida cuando el estado es 'perdida'
            if (trim($estado) === 'perdida') {
                $stmtLost = $this->db->prepare("
                    INSERT INTO mascotas_perdidas (id_mascota, ubicacion, lat, lng, detalles, estado, usuario_reporta)
                    VALUES (?, ?, ?, ?, ?, 'activo', ?)
                ");
                $stmtLost->execute([
                    $id,
                    $ubicacion ?: null,
                    $lat ?: null,
                    $lng ?: null,
                    $descripcion ?: null,
                    $usuarioId
                ]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getStatistics() {
        $stats = [];
        
        // Mascotas reunidas este mes
        $sql = "SELECT COUNT(*) AS c FROM mascotas
                WHERE estado = 'encontrada'
                  AND fecha_estado IS NOT NULL
                  AND YEAR(fecha_estado) = YEAR(CURRENT_DATE())
                  AND MONTH(fecha_estado) = MONTH(CURRENT_DATE())";
        $stats['reunidas_mes'] = (int)$this->db->query($sql)->fetchColumn();
        
        // Usuarios activos
        $stats['usuarios_activos'] = (int)$this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        
        return $stats;
    }
}
