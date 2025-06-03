<?php ob_start(); ?>
<?php if (empty($cart_items)): ?>
    <div style="display: flex; align-items: center; justify-content: space-evenly;">
        <h3>Ваш кошик порожній <a href="?page=items">Перейти до покупок</a></h3>
    </div>
<?php else: ?>
    <div style="display: flex; align-items: center; justify-content: space-evenly;">
        <h3>Ваш кошик</h3>
        <h3 class="cart-summary">
            Загальна сума: <?php echo number_format($cart_total, 2); ?> грн
        </h3>

        <button type="submit">Сплатити</button>

        <form action="?page=cart&action=clear" method="POST" style="display: inline;">
            <button type="submit">Очистити</button>
        </form>
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

                <br><br>

                <form action="?page=cart&action=remove" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                    <button type="submit" style="width: 100%;">Видалити</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
