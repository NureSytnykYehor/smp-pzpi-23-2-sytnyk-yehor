<nav>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="?page=home">Головна</a>
        |
        <a href="?page=items">Товари</a>
        |
        <a href="?page=cart">Кошик (<?php echo $cart_count ?? 0; ?>)</a>
        |
        <a href="?page=profile">Профіль</a>
        |
        <a href="?page=logout">Вихід</a>
    <?php else: ?>
        <a href="?page=login">Вхід</a>
        |
        <a href="?page=register">Реєстрація</a>
    <?php endif; ?>
</nav>
