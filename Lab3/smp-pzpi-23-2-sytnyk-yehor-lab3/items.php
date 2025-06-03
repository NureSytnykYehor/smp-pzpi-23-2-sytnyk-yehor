<?php
session_start();
require_once 'DB.php';

$db = new DB('shop.db');

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = $db->retrieve_user();
}

$items = $db->get_items();
$cart_count = $db->get_cart_count();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Сторінка товарів</title>
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

    <div class="container">
        <h2>Доступні товари</h2>
        <div class="product-list">
            <?php foreach ($items as $item): ?>
                <div>
                    <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                    <h3>Ціна: <?php echo number_format($item['price'], 2); ?> грн</h3>

                    <form action="handle_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                        <label for="quantity_<?php echo htmlspecialchars($item['id']); ?>">Кількість:</label>
                        <input type="number" id="quantity_<?php echo htmlspecialchars($item['id']); ?>" name="quantity" value="0" min="0" max="100">
                        <button type="submit">Купити</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
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
