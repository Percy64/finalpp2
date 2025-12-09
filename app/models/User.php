<?php
/**
 * User Model
 */
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, apellido, email, password, telefono, codigo_pais, direccion, foto_url, fecha_creacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['nombre'],
            $data['apellido'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['telefono'] ?? null,
            $data['codigo_pais'] ?? '+54',
            $data['direccion'] ?? null,
            $data['foto_url'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'password') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $values[] = $id;
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPets($userId) {
        $stmt = $this->db->prepare("
            SELECT m.*, cq.codigo AS codigo_qr
            FROM mascotas m
            LEFT JOIN codigos_qr cq ON m.id_qr = cq.id_qr
            WHERE m.id = ?
            ORDER BY m.fecha_registro DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
