<?php
class AuthController
{
    private $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function showLogin(): void
    {
        if (isset($_SESSION['user'])) {
            header('Location: ?page=home');
            exit();
        }

        $data = ['title' => 'Вхід в систему'];
        $this->render('login', $data);
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($username) || empty($password)) {
            $error = 'Заповніть всі поля';
        } else {
            try {
                $user = $this->db->authenticate_user($username, $password);
                if ($user) {
                    $_SESSION['user'] = $user;
                    header('Location: ?page=home');
                    exit();
                } else {
                    $error = 'Невірні дані для входу';
                }
            } catch (DbException $e) {
                $error = 'Помилка системи';
                error_log("Login error: " . $e->getMessage());
            }
        }

        $data = ['title' => 'Вхід в систему', 'error' => $error];
        $this->render('login', $data);
    }

    public function showRegister(): void
    {
        if (isset($_SESSION['user'])) {
            header('Location: ?page=home');
            exit();
        }

        $data = ['title' => 'Реєстрація'];
        $this->render('register', $data);
    }

    public function register(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $age = (int)($_POST['age'] ?? 0);
        $error = '';

        if (empty($username) || empty($password) || empty($name)) {
            $error = 'Заповніть всі обов\'язкові поля';
        } elseif (strlen($password) < 6) {
            $error = 'Пароль повинен містити мінімум 6 символів';
        } else {
            try {
                if ($this->db->register_user($username, $password, $name, $surname, $age)) {
                    $user = $this->db->authenticate_user($username, $password);
                    $_SESSION['user'] = $user;
                    header('Location: ?page=home');
                    exit();
                } else {
                    $error = 'Користувач з таким іменем вже існує';
                }
            } catch (DbException $e) {
                $error = 'Помилка реєстрації';
                error_log("Registration error: " . $e->getMessage());
            }
        }

        $data = ['title' => 'Реєстрація', 'error' => $error];
        $this->render('register', $data);
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ?page=login');
        exit();
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
