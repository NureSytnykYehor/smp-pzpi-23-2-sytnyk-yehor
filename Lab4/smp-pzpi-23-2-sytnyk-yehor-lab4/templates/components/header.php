<header>
    <h1>Продовольчий магазин "Весна"</h1>
    <?php if (isset($_SESSION['user'])): ?>
        <h3>Добрий день <?php echo htmlspecialchars($_SESSION['user']['name']); ?></h3>
    <?php endif; ?>
    <?php include 'navigation.php'; ?>
</header>
