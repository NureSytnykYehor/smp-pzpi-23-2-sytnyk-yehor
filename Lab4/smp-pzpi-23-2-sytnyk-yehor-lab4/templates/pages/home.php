<?php ob_start(); ?>
<div style="display: flex; flex-direction: column; align-items: center;">
    <img src="public/assets/logo.png" alt="logo" style="width: 90%;">
</div>
<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
