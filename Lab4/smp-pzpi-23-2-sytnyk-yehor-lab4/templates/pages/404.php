<?php ob_start(); ?>
<div style="display: flex; flex-direction: column; align-items: center; justify-content: space-between;">
    <h1 style="text-align: center;">Будь-ласка увійдіть в акаунт для доступу до цієї сторінки</h1>
    <img src="public/assets/logo.png" alt="logo" style="width: 90%;">
</div>
<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
