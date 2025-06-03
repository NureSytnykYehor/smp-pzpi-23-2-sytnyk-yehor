<?php ob_start(); ?>
<h2>Доступні товари</h2>
<div class="product-list">
    <?php foreach ($items as $item): ?>
        <div>
            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
            <h3>Ціна: <?php echo number_format($item['price'], 2); ?> грн</h3>

            <form action="?page=cart&action=add" method="POST">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                <label for="quantity_<?php echo htmlspecialchars($item['id']); ?>">Кількість:</label>
                <input type="number" id="quantity_<?php echo htmlspecialchars($item['id']); ?>" name="quantity" value="0" min="0" max="100">
                <button type="submit">Купити</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
