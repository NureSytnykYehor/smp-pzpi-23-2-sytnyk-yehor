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

        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS settings (
                    name TEXT,
                    age TEXT
                );
            ");

            if ($this->pdo->query("SELECT COUNT(*) FROM settings;")->fetchColumn() == 0) {
                $this->pdo->exec("INSERT INTO settings (name, age) VALUES ('user', 0);");
            }
        } catch (PDOException $e) {
            throw new DbException("Error initialising settings table.\nCaused by: " . $e->getMessage());
        }

        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS items (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    price REAL NOT NULL
                );
            ");

            if ($this->pdo->query("SELECT COUNT(id) FROM items;")->fetchColumn() == 0) {
                $this->pdo->exec("
                    INSERT INTO items (name, price) VALUES ('Молоко пастеризоване', 12);
                    INSERT INTO items (name, price) VALUES ('Хліб чорний', 9);
                    INSERT INTO items (name, price) VALUES ('Сир білий', 21);
                    INSERT INTO items (name, price) VALUES ('Сметана 20%', 25);
                    INSERT INTO items (name, price) VALUES ('Кефір 1%', 19);
                    INSERT INTO items (name, price) VALUES ('Вода газована', 18);
                    INSERT INTO items (name, price) VALUES ('Печиво \"Весна\"', 14);
                ");
            }
        } catch (PDOException $e) {
            throw new DbException("Error initialising items table.\nCaused by: " . $e->getMessage());
        }

        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS cart (
                    id INTEGER NOT NULL UNIQUE,
                    count INTEGER NOT NULL,
                    FOREIGN KEY(id) REFERENCES item(id)
                );
            ");
        } catch (PDOException $e) {
            throw new DbException("Error initialising cart table.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Updates user information in the database
     *
     * @param string $name
     * @param int $age
     * @return void
     * @throws DbException If there's a database error.
     */
    public function update_user($name, $age): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE settings SET name = :name, age = :age;");

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            throw new DbException("Error updating user info.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Fetches all items from the database.
     *
     * @return array[]
     * @throws DbException If there's a database error.
     */
    public function get_items(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name, price FROM items ORDER BY id;");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException("Error retrieving data from the items table.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Fetches all items in the cart from the database without price info.
     *
     * @return array[]
     * @throws DbException If there's a database error.
     */
    public function get_cart_no_price(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT name, count FROM cart
                INNER JOIN items ON cart.id = items.id
                ORDER BY cart.id;"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException("Error inserting init data in the items table.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Fetches all items in the cart from the database.
     *
     * @return array[]
     * @throws DbException If there's a database error.
     */
    public function get_cart(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT
                    cart.id, name, price, count, price*count as total_price
                FROM cart
                INNER JOIN items ON cart.id = items.id
                ORDER BY cart.id;"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException("Error inserting init data in the items table.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Add item to the cart
     *
     * @param int $id
     * @param int $count
     *
     * @return bool
     * @throws DbException If there's a database error.
     */
    public function add_to_cart($id, $count): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO cart
                    (id, count)
                VALUES
                    (:id, :count)
                ON CONFLICT(id)
                    DO UPDATE SET
                        count = :count
                WHERE id = :id;"
            );
            return $stmt->execute(['id' => $id, 'count' => $count]);
        } catch (PDOException $e) {
            throw new DbException("Error adding item to the cart.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Remove an item from the cart
     *
     * @param int $id
     *
     * @return bool
     * @throws DbException If there's a database error.
     */
    public function remove_from_cart($id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM cart WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new DbException("Error removimg item from the cart.\nCaused by: " . $e->getMessage());
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
        $items = $this->db->get_items();
        array_unshift($items, ['id' => "№", 'name' => "НАЗВА", 'price' => "ЦІНА"]);
        array_push($items, ['id' => " ", 'name' => "-----------", 'price' => ""]);
        array_push($items, ['id' => "0", 'name' => "ПОВЕРНУТИСЯ", 'price' => ""]);
        $columns = $this->count_columns($items);

        while (true) {
            $this->print_lits($items, $columns);

            $id = readline("Виберіть товар: ");

            if ($id == '0') {
                break;
            }

            echo "\n";

            $selected = null;
            foreach ($items as $item) {
                if ($item['id'] === (int)$id)
                    $selected = $item;
            }

            if ($selected == null) {
                echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
                continue;
            }

            echo "Вибрано: {$selected['name']}\n";

            $count = (int)readline("Введіть кількість, штук: ");

            if ($count > 100) {
                echo "ПОМИЛКА! Не можна додати більше 100 одиниць товару в кошик\n";
                continue;
            }

            if ($count < 0) {
                echo "ПОМИЛКА! Кількість не може бути від'ємною\n";
                continue;
            }

            if ($count == 0) {
                echo "ВИДАЛЯЮ З КОШИКА\n";
                $this->db->remove_from_cart($id);
            } else {
                $this->db->add_to_cart($id, $count);
            }

            $cart = $this->db->get_cart_no_price();
            if (count($cart) == 0) {
                echo "КОШИК ПОРОЖНІЙ\n";
            } else {
                echo "\nУ КОШИКУ:\n";
                array_unshift($cart, ['name' => "НАЗВА", 'count' => "КІЛЬКІСТЬ"]);
                $cart_columns = $this->count_columns($cart);
                $this->print_lits($cart, $cart_columns);
                echo "\n";
            }
        }

        $this->state = State::Menu;
    }
    private function checkout(): void
    {
        $cart = $this->db->get_cart();
        if (count($cart) == 0) {
            echo "КОШИК ПОРОЖНІЙ\n";
            $this->state = State::Menu;
            return;
        } else {
            echo "У КОШИКУ:\n";
            array_unshift($cart, ['id' => "№", 'name' => "НАЗВА", 'price' => "ЦІНА", 'count' => "КІЛЬКІСТЬ", 'total_price' => "ВАРТІСТЬ"]);
            $cart_columns = $this->count_columns($cart);
            $this->print_lits($cart, $cart_columns);
        }

        $total_price = array_reduce($cart, function ($carry, $item) {
            return $carry + (int)$item['total_price'];
        }, 0);
        echo "РАЗОМ ДО СПЛАТИ: {$total_price}\n";

        $this->state = State::Menu;
    }
    private function settings(): void
    {
        while (true) {
            $name = readline("Ваше ім'я: ");
            if ($name !== "" && preg_match("/[a-zA-Z]+/", $name))
                break;
        }

        while (true) {
            $age = readline("Ваш вік: ");

            if (!filter_var($age, FILTER_VALIDATE_INT)) {
                echo "ПОМИЛКА! Вік користувача потрібно вказати числом\n\n";
                continue;
            }

            if ($age < 7 || $age > 150) {
                echo "ПОМИЛКА! Користувач повинен мати вік від 7 та до 150 років\n\n";
                continue;
            }

            break;
        }

        echo "\n";

        $this->db->update_user($name, $age);

        $this->state = State::Menu;
    }

    /**
     * @param array<array> $items
     * @return array<int>
     */
    private function count_columns($items): array
    {
        $columns = [];
        foreach ($items as $item) {
            foreach ($item as $field => $value) {
                if (!key_exists($field, $columns))
                    $columns[$field] = mb_strlen($value);
                else
                    $columns[$field] = max(mb_strlen($value), $columns[$field]);
            }
        }

        return $columns;
    }
    /**
     * @param array $element
     * @param array<int> $columns
     */
    private function pad_row($element, $columns): string
    {
        $result = [];
        foreach ($element as $field => $value)
            $result[] = mb_str_pad($value, $columns[$field], ' ', STR_PAD_RIGHT);

        return implode("  ", $result);
    }
    /**
     * @param array<array> $items
     * @param array<int> $columns
     */
    private function print_lits($items, $columns): void
    {
        foreach ($items as $item)
            echo $this->pad_row($item, $columns) . "\n";
    }
}

try {
    $app = new App("db.sqlite");
} catch (AppException $e) {
    echo $e;
    exit(1);
}

$app->poll();
