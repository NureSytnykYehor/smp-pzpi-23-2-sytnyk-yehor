<?php
session_start();
require_once 'DB.php';

$db = new DB('shop.db');

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = $db->retrieve_user();
}

$cart_count = $db->get_cart_count();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Продовольчий магазин "Весна"</h1>
        <h3> Добрий день <?php echo $_SESSION['user']['name'] ?> </h3>
        <nav>
            <a href="index.php">Головна</a>
            |
            <a href="items.php">Товари</a>
            |
            <a href="cart.php">Кошик (<?php echo $cart_count ?? 0; ?>)</a>
        </nav>
    </header>

    <div class="container" style="display: flex; flex-direction: column; align-items: center;">
        <img src="logo.png" alt="logo" style="width: 90%;">
    </div>

    <footer>
        <nav>
            <a href="index.php">Головна</a>
            |
            <a href="items.php">Товари</a>
            |
            <a href="cart.php">Кошик (<?php echo $cart_count ?? 0; ?>)</a>
        </nav>
        <p>&copy; <?php echo date("Y"); ?> ТОВ "Весна". Усі права захищені.</p>
    </footer>
</body>

</html>
