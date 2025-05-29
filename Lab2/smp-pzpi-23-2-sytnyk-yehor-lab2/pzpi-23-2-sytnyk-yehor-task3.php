#!/usr/bin/php

<?php

class DbException extends Exception {}
class AppException extends Exception {}

enum State
{
    case Hello;
    case Menu;
    case Items;
    case Checkout;
    case Settins;
    case Exit;
}

class DB
{
    private $pdo;

    /**
     * Initializes database
     *
     * @param string $db_path
     * @throws DbException If there's a database error.
     */
    public function __construct($db_path)
    {
        try {
            $this->pdo = new PDO("sqlite:" . $db_path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new DbException("Connection to DB failed.\nCaused by: " . $e->getMessage());
        }
    }
}

class App
{
    private $db;
    private $state;

    private $menu_ops = <<<'END'
    1 Вибрати товари
    2 Отримати підсумковий рахунок
    3 Налаштувати свій профіль
    0 Вийти з програми
    END;
    private $hello = <<<'END'
    ################################
    # ПРОДОВОЛЬЧИЙ МАГАЗИН "ВЕСНА" #
    ################################
    END;


    /**
     * @param string $db_path
     */
    public function __construct($db_path)
    {
        try {
            $this->db = new DB($db_path);
            $this->state = State::Hello;
        } catch (DbException $e) {
            throw new AppException("Error initializing app.\nCaused by: " . $e);
        }
    }

    public function poll(): void
    {
        while ($this->state != State::Exit) {
            switch ($this->state) {
                case State::Hello:
                    $this->hello();
                    break;
                case State::Menu:
                    $this->menu();
                    break;
                case State::Items:
                    $this->items();
                    break;
                case State::Checkout:
                    $this->checkout();
                    break;
                case State::Settins:
                    $this->settings();
                    break;

                default:
                    break;
            }
        }
    }

    private function menu(): void
    {
        echo "\n";
        echo "$this->menu_ops\n";

        $op = readline('Введіть команду: ');
        switch ($op) {
            case '1':
                $this->state = State::Items;
                break;
            case '2':
                $this->state = State::Checkout;
                break;
            case '3':
                $this->state = State::Settins;
                break;
            case '0':
                $this->state = State::Exit;
                break;

            default:
                echo "ПОМИЛКА! Введіть правильну команду\n";
                break;
        }

        echo "\n";
    }
    private function hello(): void
    {
        echo "$this->hello\n";
        $this->state = State::Menu;
    }
    private function items(): void
    {
        $this->state = State::Menu;
    }
    private function checkout(): void
    {
        $this->state = State::Menu;
    }
    private function settings(): void
    {
        $this->state = State::Menu;
    }

try {
    $app = new App("db.sqlite");
} catch (AppException $e) {
    echo $e;
    exit(1);
}

$app->poll();
