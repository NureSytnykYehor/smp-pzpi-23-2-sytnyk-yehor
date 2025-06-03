<?php if (isset($error) && !empty($error)): ?>
    <div style="color: red; margin-bottom: 20px; padding: 10px; border: 1px solid red; background: #ffebee;">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
