<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id_mascota = isset($_POST['id_mascota']) ? (int)$_POST['id_mascota'] : 0;
$estado = isset($_POST['estado']) ? $_POST['estado'] : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

// Datos opcionales de ubicación (solo relevantes si estado = 'perdida')
$latitud = isset($_POST['latitud']) && $_POST['latitud'] !== '' ? $_POST['latitud'] : null;
$longitud = isset($_POST['longitud']) && $_POST['longitud'] !== '' ? $_POST['longitud'] : null;
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : null;
$referencia = isset($_POST['referencia']) ? trim($_POST['referencia']) : null;
$fecha_perdida = isset($_POST['fecha_perdida']) && $_POST['fecha_perdida'] !== '' ? $_POST['fecha_perdida'] : null; // formato esperado: Y-m-d H:i:s

// Normalizar tipos
if ($latitud !== null && !is_numeric($latitud)) { $latitud = null; }
if ($longitud !== null && !is_numeric($longitud)) { $longitud = null; }
if ($direccion !== null && $direccion !== '' && strlen($direccion) > 255) { $direccion = substr($direccion, 0, 255); }

// Validar estado
$estados_validos = ['normal', 'perdida', 'encontrada'];
if (!in_array($estado, $estados_validos)) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit;
}

if ($id_mascota === 0) {
    echo json_encode(['success' => false, 'message' => 'ID de mascota inválido']);
    exit;
}

try {
    // Verificar que la mascota pertenece al usuario
    $sql_check = "SELECT id_mascota FROM mascotas WHERE id_mascota = ? AND id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$id_mascota, $usuario_id]);
    
    if (!$stmt_check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para modificar esta mascota']);
        exit;
    }
    
    // Si se va a marcar como perdida, verificar que exista la tabla de ubicaciones
    if ($estado === 'perdida') {
        try {
            $check = $pdo->query("SHOW TABLES LIKE 'mascotas_perdidas'");
            if (!$check || $check->rowCount() === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Falta la tabla mascotas_perdidas. Importá database/crear_tabla_mascotas_perdidas.sql desde phpMyAdmin.',
                    'code' => 'missing_table'
                ]);
                exit;
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error verificando tablas: ' . $e->getMessage()]);
            exit;
        }
    }

    // Iniciar transacción para mantener consistencia
    $pdo->beginTransaction();

    // Actualizar estado
    $fecha_estado = ($estado === 'normal') ? null : date('Y-m-d H:i:s');
    // Mantener compatibilidad con la columna legado `perdido` (1 = perdida, 0 = otros estados)
    $perdido = ($estado === 'perdida') ? 1 : 0;
    $sql_update = "UPDATE mascotas SET estado = ?, perdido = ?, fecha_estado = ?, descripcion_estado = ? WHERE id_mascota = ? AND id = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $result = $stmt_update->execute([$estado, $perdido, $fecha_estado, $descripcion, $id_mascota, $usuario_id]);

    if (!$result) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        exit;
    }

    // Si se marca como perdida, registrar ubicación si se envió
    $id_perdida = null;
    if ($estado === 'perdida') {
        $sql_insert_perdida = "INSERT INTO mascotas_perdidas (id_mascota, latitud, longitud, direccion, referencia, fecha_perdida)
                               VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_perdida = $pdo->prepare($sql_insert_perdida);
        $fecha_perdida_eff = $fecha_perdida ?: $fecha_estado; // usar ahora si no vino
        $stmt_perdida->execute([
            $id_mascota,
            $latitud !== null ? (float)$latitud : null,
            $longitud !== null ? (float)$longitud : null,
            $direccion !== null && $direccion !== '' ? $direccion : null,
            $referencia !== null && $referencia !== '' ? $referencia : null,
            $fecha_perdida_eff
        ]);
        $id_perdida = (int)$pdo->lastInsertId();
    }

    $pdo->commit();

    if (true) {
        $mensaje = '';
        switch($estado) {
            case 'perdida':
                $mensaje = 'Mascota reportada como perdida';
                break;
            case 'encontrada':
                $mensaje = 'Mascota reportada como encontrada';
                break;
            case 'normal':
                $mensaje = 'Estado actualizado a normal';
                break;
        }
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'data' => [
                'id_mascota' => $id_mascota,
                'estado' => $estado,
                'id_perdida' => $id_perdida,
                'ubicacion' => [
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'direccion' => $direccion,
                    'referencia' => $referencia,
                    'fecha_perdida' => $fecha_perdida ?: $fecha_estado,
                ]
            ]
        ]);
    }
} catch(PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
