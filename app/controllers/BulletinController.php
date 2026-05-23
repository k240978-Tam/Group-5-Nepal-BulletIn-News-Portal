<?php
namespace App\Controllers;
use App\Core\Controller;

class BulletinController extends Controller {
    public function index() {
        $this->view('bulletin/index', []);
    }
}
