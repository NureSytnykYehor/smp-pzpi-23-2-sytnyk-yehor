<?php ob_start(); ?>
<div style="max-width: 400px; margin: 0 auto;">
    <h2>Вхід в систему</h2>

    <?php require 'templates/components/error.php' ?>

    <form method="POST" action="?page=login">
        <div style="margin-bottom: 15px;">
            <label for="username">Ім'я користувача:</label><br>
            <input type="text" id="username" name="username" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password">Пароль:</label><br>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" style="width: 100%;">Увійти</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        <a href="?page=register">Немає акаунта? Зареєструватися</a>
    </p>
</div>
<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
