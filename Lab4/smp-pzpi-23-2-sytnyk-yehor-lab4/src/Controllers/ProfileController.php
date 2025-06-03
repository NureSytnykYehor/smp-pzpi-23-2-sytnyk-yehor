<?php
class ProfileController
{
    private $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function showProfile(): void
    {
        $data = ['title' => 'Профіль'];
        $this->render('profile', $data);
    }

    public function updateProfile(): void
    {
        $user = $_SESSION['user'];
        $id = $user['id'];
        $name = trim($_POST['name'] ?? $user['name']);
        $surname = trim($_POST['surname'] ?? $user['surname']);
        $age = (int)($_POST['age'] ?? $user['age']);
        $description = trim($_POST['description'] ?? $user['description']);
        $photo_path = $user['photo_path'];
        $error = '';

        if (mb_strlen($name) < 2 && mb_strlen($surname) < 2) {
            $error = "Ім'я та прізвище повинні мати довжину більше 1 символа";
            $data = ['title' => 'Профіль', 'error' => $error];
            $this->render('profile', $data);
            return;
        }

        if (mb_strlen($description) < 50) {
            $error = 'Біоаграфія не може бути менше 50 символів';
            $data = ['title' => 'Профіль', 'error' => $error];
            $this->render('profile', $data);
            return;
        }

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            if (!in_array($_FILES['photo']['type'], ['image/jpeg', 'image/png'])) {
                $error = 'Неправильний формат файлу';
                $data = ['title' => 'Профіль', 'error' => $error];
                $this->render('profile', $data);
                return;
            }

            $uploads = 'uploads/';
            if (!is_dir($uploads)) {
                mkdir($uploads, 0755, true);
            }

            $new_path = $uploads . $id . '-' . time() . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $new_path)) {
                $error = 'Помилка під час переміщення файлу';
                $data = ['title' => 'Профіль', 'error' => $error];
                $this->render('profile', $data);
                return;
            }

            $photo_path = $new_path;
        }

        try {
            $success = $this->db->update_user($id, $name, $surname, $description, $photo_path, $age);

            if ($success) {
                $user = $this->db->get_user_by_id($id);
                $_SESSION['user'] = $user;
            } else {
                $error = 'Під час оновлення даних сталася помилка';
            }

            header('Location: ?page=profile');
            exit();
        } catch (DbException $e) {
            $error = 'Помилка оновлення профілю';
            error_log("Profile update error: " . $e->getMessage());
        }

        $data = ['title' => 'Профіль', 'error' => $error];
        $this->render('profile', $data);
    }

    /**
     * @param array<int,mixed> $data
     */
    private function render(string $template, array $data = []): void
    {
        extract($data);
        include 'templates/pages/' . $template . '.php';
    }
}
