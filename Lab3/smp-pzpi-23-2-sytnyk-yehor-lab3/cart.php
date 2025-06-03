<?php
session_start();
require_once 'DB.php';

$db = new DB("shop.db");

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = $db->retrieve_user();
}

$cart_items = $db->get_cart();
$cart_total = $db->get_cart_total();
$cart_count = $db->get_cart_count();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Кошик</title>
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
        <?php if (empty($cart_items)): ?>
            <div style="display: flex; align-items: center; justify-content: space-evenly;">
                <h3>Ваш кошик порожній <a href="items.php">Перейти до покупок</a> </h3>
            </div>
        <?php else: ?>
            <div style="display: flex; align-items: center; justify-content: space-evenly;">
                <h3>Ваш кошик</h3>
                <h3 class="cart-summary">
                    Загальна сума: <?php echo number_format($cart_total, 2); ?> грн
                </h3>

                <button type="submit">Сплатити</button>

                <button onclick="fetch('handle_cart.php', {'method': 'DELETE'}).then(_ => { location.reload(); });">Очистити</button>
            </div>

            <div class="product-list">
                <?php foreach ($cart_items as $item): ?>
                    <div>
                        <h2>
                            <?php echo htmlspecialchars($item['name']); ?>
                            <br>
                            <?php echo htmlspecialchars($item['count']); ?> шт.
                        </h2>

                        <span>Ціна за одиницю: <?php echo number_format($item['price'], 2); ?> грн</span>
                        <br>
                        <span>Загальна ціна: <?php echo number_format($item['total_price'], 2); ?> грн</span>

                        <br>
                        <br>

                        <button
                            style="width: 100%;"
                            onclick="
                            fetch(
                                'handle_cart.php?product_id=<?php echo htmlspecialchars($item['id']); ?>',
                                { 'method': 'DELETE' }
                            ).then(_ => { location.reload(); });">
                            Видалити
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
