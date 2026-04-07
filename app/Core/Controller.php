<?php

namespace App\Core;

class Controller {
    protected function view($view, $data = []) {
        extract($data);
        include_once __DIR__ . "/../Views/layouts/main.php";
    }

    protected function render($view, $data = []) {
        extract($data);
        include_once __DIR__ . "/../Views/$view.php";
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
