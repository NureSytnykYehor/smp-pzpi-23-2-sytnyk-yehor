<?php

session_start();

require_once 'src/Database/DB.php';
require_once 'src/Database/CartRepository.php';
require_once 'src/Controllers/HomeController.php';
require_once 'src/Controllers/ItemsController.php';
require_once 'src/Controllers/CartController.php';
require_once 'src/Controllers/AuthController.php';
require_once 'src/Controllers/ProfileController.php';

$db = new DB("shop.db");

$request = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$protected_pages = ['home', 'items', 'cart', 'profile'];
$public_pages = ['login', 'register'];

if (in_array($request, $protected_pages) && !isset($_SESSION['user'])) {
    header('Location: ?page=404');
    exit();
}

try {
    switch ($request) {
        case 'home':
            $controller = new HomeController($db);
            $controller->index();
            break;

        case 'items':
            $controller = new ItemsController($db);
            $controller->index();
            break;

        case 'cart':
            $controller = new CartController($db);
            if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->add();
            } elseif ($action === 'remove' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->remove();
            } elseif ($action === 'clear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->clear();
            } else {
                $controller->index();
            }
            break;

        case 'login':
            $controller = new AuthController($db);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->showLogin();
            }
            break;

        case 'register':
            $controller = new AuthController($db);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->register();
            } else {
                $controller->showRegister();
            }
            break;

        case 'logout':
            $controller = new AuthController($db);
            $controller->logout();
            break;

        case 'profile':
            $controller = new ProfileController($db);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->updateProfile();
            } else {
                $controller->showProfile();
            }
            break;

        default:
            http_response_code(404);
            include 'templates/pages/404.php';
            break;
    }
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    http_response_code(500);
    include 'templates/pages/error.php';
}
