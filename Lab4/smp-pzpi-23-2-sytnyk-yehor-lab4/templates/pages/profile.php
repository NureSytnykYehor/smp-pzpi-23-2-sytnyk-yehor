<?php ob_start(); ?>

<?php require 'templates/components/error.php' ?>

<div style="display: flex; flex-direction: column; align-items: center; justify-content: space-between;">
    <div style="width: 150px; height: 150px; border: 1px solid black; display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
        <img src="<?= htmlspecialchars($_SESSION['user']['photo_path']) ?>" alt="Profile Image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
    </div>

    <form style="display: grid; gap: 15px;" method="post" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*">

        <label style="font-weight: bold;">Ім'я</label>
        <input type="text" value="<?php echo $_SESSION['user']['name'] ?>" name="name">

        <label style="font-weight: bold;">Фамілія</label>
        <input type="text" value="<?php echo $_SESSION['user']['surname'] ?>" name="surname">

        <label style="font-weight: bold;">Вік</label>
        <input type="number" min="16" max="150" value="<?php echo $_SESSION['user']['age'] ?>" name="age">

        <label style="font-weight: bold;">Про себе</label>
        <textarea style="height: 50px; resize: vertical;" name="description"><?php echo $_SESSION['user']['description'] ?></textarea>

        <button>Сохранить</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'templates/layout.php';
?>
