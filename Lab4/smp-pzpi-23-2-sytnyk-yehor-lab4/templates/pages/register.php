<?php ob_start(); ?>
<div style="max-width: 400px; margin: 0 auto;">
    <h2>Реєстрація</h2>

    <?php require 'templates/components/error.php' ?>

    <form method="POST" action="?page=register">
        <div style="margin-bottom: 15px;">
            <label for="username">Логін *:</label><br>
            <input type="text" id="username" name="username" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password">Пароль * (мінімум 6 символів):</label><br>
            <input type="password" name="password" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="name">Ім'я *:</label><br>
            <input type="text" name="name" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="surname">Прізвище *:</label><br>
            <input type="text" name="surname" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="age">Вік *:</label><br>
            <input type="number" name="age" min="16" max="150" style="width: 97%;" required>
        </div>

        <button type="submit" style="width: 100%;">Зареєструватися</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        <a href="?page=login">Вже є акаунт? Увійти</a>
    </p>
</div>
<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
