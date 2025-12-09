<?php
/**
 * Base Controller Class
 */
class Controller {
    
    protected function loadModel($model) {
        require_once MODEL_PATH . '/' . $model . '.php';
        return new $model();
    }

    protected function view($view, $data = []) {
        extract($data);
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: $view");
        }
    }

    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isAuthenticated() {
        return isset($_SESSION['usuario_id']);
    }

    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/login');
        }
    }

    protected function getCurrentUser() {
        if ($this->isAuthenticated()) {
            return [
                'id' => $_SESSION['usuario_id'],
                'nombre' => $_SESSION['usuario_nombre'] ?? '',
                'email' => $_SESSION['usuario_email'] ?? ''
            ];
        }
        return null;
    }

    protected function uploadImage($file, $type = 'mascota', $userId = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed)) {
            return false;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $type . '_' . ($userId ?? time()) . '_' . time() . '.' . $extension;
        $targetDir = ROOT_PATH . "/assets/images/" . $type . "s/";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $targetPath = $targetDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return "/assets/images/" . $type . "s/" . $filename;
        }
        
        return false;
    }
}
