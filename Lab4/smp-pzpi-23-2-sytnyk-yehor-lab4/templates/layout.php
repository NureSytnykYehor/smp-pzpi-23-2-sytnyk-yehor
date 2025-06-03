<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title ?? 'Продовольчий магазин "Весна"'); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <div class="container">
        <?php echo $content; ?>
    </div>

    <?php include 'components/footer.php'; ?>
</body>

</html>
