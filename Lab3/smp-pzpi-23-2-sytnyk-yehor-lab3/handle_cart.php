<?php
session_start();
require_once 'DB.php';

$db_path = 'shop.db';
$db = new DB($db_path);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($product_id !== false && $product_id !== null && $quantity !== false && $quantity !== null) {
            try {
                $db->add_to_cart($product_id, $quantity);
            } catch (DbException $e) {
                error_log("Cart handling error: " . $e->getMessage());
            }
        }
        header('Location: items.php');
        break;

    case 'DELETE':
        $product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);

        if ($product_id === null) {
            try {
                $db->empty_cart();
            } catch (DbException $e) {
                error_log("Cart handling error: " . $e->getMessage());
            }
        } else if ($product_id !== false) {
            try {
                $db->remove_from_cart($product_id);
            } catch (DbException $e) {
                error_log("Cart handling error: " . $e->getMessage());
            }
        }

        header('Location: cart.php');
        break;

    default:
        break;
}

exit();
