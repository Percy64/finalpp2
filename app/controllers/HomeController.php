<?php
/**
 * Home Controller
 */
class HomeController extends Controller {
    
    private $petModel;

    public function __construct() {
        $this->petModel = $this->loadModel('Pet');
    }

    public function index() {
        $mascotas = $this->petModel->findLostPets();
        $estadisticas = $this->petModel->getStatistics();
        
        $this->view('home/index', [
            'mascotas' => $mascotas,
            'estadisticas' => $estadisticas,
            'usuario_logueado' => $this->isAuthenticated()
        ]);
    }
}
