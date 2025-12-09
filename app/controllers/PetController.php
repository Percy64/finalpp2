<?php
/**
 * Pet Controller
 */
class PetController extends Controller {
    
    private $petModel;

    public function __construct() {
        $this->petModel = $this->loadModel('Pet');
    }

    public function register() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $currentUser['id'],
                'nombre' => trim($_POST['nombre'] ?? ''),
                'especie' => trim($_POST['especie'] ?? ''),
                'raza' => trim($_POST['raza'] ?? ''),
                'edad' => trim($_POST['edad'] ?? ''),
                'color' => trim($_POST['color'] ?? ''),
                'genero' => trim($_POST['genero'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? '')
            ];

            $errors = [];
            
            if (empty($data['nombre'])) $errors[] = 'El nombre es requerido';
            if (empty($data['especie'])) $errors[] = 'La especie es requerida';

            // Upload foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto = $this->uploadImage($_FILES['foto'], 'mascota', $currentUser['id']);
                if ($foto) {
                    $data['foto_url'] = $foto;
                }
            }

            if (empty($errors)) {
                $petId = $this->petModel->create($data);
                
                // Generar QR y guardar referencia en BD
                require_once ROOT_PATH . '/app/includes/QRGenerator.php';
                $qrGen = new QRGenerator();
                $qrInfo = $qrGen->generarQRMascota($petId);
                if (!$qrInfo['success']) {
                    $qrInfo = $qrGen->generarQRSimple($petId); // fallback
                }
                $pdo = Database::getInstance()->getConnection();
                $qrGen->actualizarQREnBD($pdo, $petId, $qrInfo);
                
                $_SESSION['success_message'] = 'Mascota registrada correctamente';
                $this->redirect('/mascota/' . $petId);
            }

            $this->view('pet/register', ['errors' => $errors, 'data' => $data]);
        } else {
            $this->view('pet/register');
        }
    }

    public function profile($id) {
        $pet = $this->petModel->findById($id);
        
        if (!$pet) {
            $this->redirect('/');
        }

        // Solo el dueño puede ver el perfil completo
        // Otros usuarios son redirigidos a la vista pública (qr_info)
        $isOwner = false;
        if ($this->isAuthenticated()) {
            $currentUser = $this->getCurrentUser();
            $isOwner = ($pet['id'] == $currentUser['id']);
        }

        if (!$isOwner) {
            // Redirigir a la vista pública
            $this->redirect('/qr/' . $id);
            return;
        }

        $this->view('pet/profile', [
            'pet' => $pet,
            'isOwner' => $isOwner
        ]);
    }

    public function edit($id) {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        
        $pet = $this->petModel->findById($id);
        
        if (!$pet || $pet['id'] != $currentUser['id']) {
            $_SESSION['error_message'] = 'No tienes permiso para editar esta mascota';
            $this->redirect('/perfil');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'especie' => trim($_POST['especie'] ?? ''),
                'raza' => trim($_POST['raza'] ?? ''),
                'edad' => trim($_POST['edad'] ?? ''),
                'color' => trim($_POST['color'] ?? ''),
                'genero' => trim($_POST['genero'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? '')
            ];

            // Upload foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto = $this->uploadImage($_FILES['foto'], 'mascota', $currentUser['id']);
                if ($foto) {
                    $data['foto_url'] = $foto;
                }
            }

            $this->petModel->update($id, $data);
            $_SESSION['success_message'] = 'Mascota actualizada correctamente';
            $this->redirect('/mascota/' . $id);
        }

        $this->view('pet/edit', ['pet' => $pet]);
    }

    public function delete($id) {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        
        $pet = $this->petModel->findById($id);
        
        if (!$pet || $pet['id'] != $currentUser['id']) {
            $_SESSION['error_message'] = 'No tienes permiso para eliminar esta mascota';
            $this->redirect('/perfil');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminar'])) {
            if ($this->petModel->delete($id, $currentUser['id'])) {
                $_SESSION['success_message'] = 'Mascota eliminada correctamente';
            } else {
                $_SESSION['error_message'] = 'Error al eliminar la mascota';
            }
            $this->redirect('/perfil');
        }

        $this->view('pet/delete', ['pet' => $pet]);
    }

    public function changeStatus($id) {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        
        $pet = $this->petModel->findById($id);
        
        if (!$pet || $pet['id'] != $currentUser['id']) {
            $this->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estado = trim($_POST['estado'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $ubicacion = trim($_POST['ubicacion_perdida'] ?? '');
            $lat = isset($_POST['lat']) ? trim($_POST['lat']) : null;
            $lng = isset($_POST['lng']) ? trim($_POST['lng']) : null;
            
            if (!in_array($estado, ['normal', 'perdida', 'encontrada'])) {
                $this->json(['success' => false, 'message' => 'Estado inválido'], 400);
            }

            // Validar ubicación obligatoria si se marca como perdida
            if ($estado === 'perdida' && $ubicacion === '') {
                $this->json(['success' => false, 'message' => 'Ingresa dónde se perdió'], 400);
            }

            if ($this->petModel->changeStatus($id, $estado, $descripcion, $ubicacion, $lat, $lng, $currentUser['id'])) {
                $this->json(['success' => true, 'message' => 'Estado actualizado']);
            } else {
                $this->json(['success' => false, 'message' => 'Error al actualizar'], 500);
            }
        }
    }

    public function map() {
        // Cargar todas las mascotas (priorizando perdidas)
        $mascotas = $this->petModel->findLostPets();
        
        // Si no hay perdidas, mostrar todas
        if (empty($mascotas)) {
            $sql = "SELECT m.* FROM mascotas m ORDER BY m.fecha_registro DESC";
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $mascotas = $stmt->fetchAll();
        }
        
        // Obtener estadísticas
        $estadisticas = $this->petModel->getStatistics();
        
        $this->view('pet/map', [
            'mascotas' => $mascotas,
            'estadisticas' => $estadisticas
        ]);
    }

    public function qrInfo($id) {
        $pet = $this->petModel->findById($id);
        
        if (!$pet) {
            $this->redirect('/');
        }

        $this->view('pet/qr_info', ['pet' => $pet]);
    }
}
