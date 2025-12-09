<?php
require_once __DIR__ . '/../../config/config.php';

class LegalController extends Controller {
    
    public function terminos() {
        // Mostrar términos y condiciones
        $this->view('legal/terminos-condiciones');
    }
    
    public function mision() {
        // Mostrar misión y visión
        $this->view('legal/mision_vision');
    }
}
