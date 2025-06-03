<?php

class DbException extends Exception {}

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
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    name TEXT NOT NULL,
                    surname TEXT NOT NULL,
                    description TEXT NULL,
                    photo_path TEXT NULL,
                    age INTEGER DEFAULT 0
                );
            ");

            if ($this->pdo->query("SELECT COUNT(*) FROM users;")->fetchColumn() == 0) {
                $default_password = password_hash('admin123', PASSWORD_DEFAULT);
                $this->pdo->exec("
                    INSERT INTO users (username, password, name, surname, description, age)
                    VALUES ('admin', '$default_password', 'Адміністратор', 'Адміністратор', 'Адміністратор', 25);
                ");
            }
        } catch (PDOException $e) {
            throw new DbException("Error initialising users table.\nCaused by: " . $e->getMessage());
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
                    INSERT INTO items (name, price) VALUES ('Молоко пастеризоване', 32.50);
                    INSERT INTO items (name, price) VALUES ('Хліб чорний', 18.00);
                    INSERT INTO items (name, price) VALUES ('Сир білий', 85.00);
                    INSERT INTO items (name, price) VALUES ('Сметана 20%', 45.80);
                    INSERT INTO items (name, price) VALUES ('Кефір 1%', 28.50);
                    INSERT INTO items (name, price) VALUES ('Вода газована', 25.00);
                    INSERT INTO items (name, price) VALUES ('Печиво \"Весна\"', 42.30);
                    INSERT INTO items (name, price) VALUES ('Масло вершкове', 125.00);
                    INSERT INTO items (name, price) VALUES ('Йогурт натуральний', 38.90);
                    INSERT INTO items (name, price) VALUES ('Сік апельсиновий', 55.00);
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
                    FOREIGN KEY(id) REFERENCES items(id) ON DELETE CASCADE
                );
            ");
        } catch (PDOException $e) {
            throw new DbException("Error initialising cart table.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Updates user by id
     * @param int $id
     * @param string $name
     * @param string $surname
     * @param string $description
     * @param int $age
     * @param string $photo_path
     */
    public function update_user($id, $name, $surname, $description, $photo_path, $age): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users
                SET name = :name, surname = :surname, description = :description, age = :age, photo_path = :photo_path
                WHERE id = :id
            ");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':photo_path', $photo_path, PDO::PARAM_STR);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new DbException("Error updating user.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Authenticate user by username and password
     * @param mixed $username
     * @param mixed $password
     */
    public function authenticate_user($username, $password): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, name, surname, description, photo_path, age FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stmt = $this->pdo->prepare("SELECT password FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                $stored_password = $stmt->fetchColumn();

                if (password_verify($password, $stored_password)) {
                    return $user;
                }
            }
            return null;
        } catch (PDOException $e) {
            throw new DbException("Error authenticating user.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Register new user
     * @param mixed $username
     * @param mixed $password
     * @param mixed $name
     * @param mixed $surname
     * @param mixed $age
     */
    public function register_user($username, $password, $name, $surname, $age): bool
    {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (username, password, name, surname, age) VALUES (:username, :password, :name, :surname, :age)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new DbException("Error registering user.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Get user by ID
     * @param mixed $id
     */
    public function get_user_by_id($id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, name, surname, description, age, photo_path FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new DbException("Error retrieving user by ID.\nCaused by: " . $e->getMessage());
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
     * Fetches a specific item by ID
     *
     * @param int $id
     * @return array|null
     * @throws DbException If there's a database error.
     */
    public function get_item_by_id($id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, price FROM items WHERE id = :id;");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new DbException("Error retrieving item by ID.\nCaused by: " . $e->getMessage());
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
                "SELECT items.name, cart.count
                FROM cart
                INNER JOIN items ON cart.id = items.id
                ORDER BY cart.id;"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException("Error retrieving cart items without price.\nCaused by: " . $e->getMessage());
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
                    cart.id,
                    items.name,
                    items.price,
                    cart.count,
                    ROUND(items.price * cart.count, 2) as total_price
                FROM cart
                INNER JOIN items ON cart.id = items.id
                ORDER BY cart.id;"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException("Error retrieving cart items.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Get total items count in cart
     *
     * @return int
     * @throws DbException If there's a database error.
     */
    public function get_cart_count(): int
    {
        try {
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(count), 0) FROM cart;");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new DbException("Error getting cart count.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Get total price of all items in cart
     *
     * @return float
     * @throws DbException If there's a database error.
     */
    public function get_cart_total(): float
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT COALESCE(SUM(items.price * cart.count), 0.0)
                FROM cart
                INNER JOIN items ON cart.id = items.id;"
            );
            return (float)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new DbException("Error calculating cart total.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Add item to the cart or update its quantity.
     *
     * @param int $id
     * @param int $count
     *
     * @return bool
     * @throws DbException If there's a database error or item doesn't exist.
     */
    public function add_to_cart($id, $count): bool
    {
        try {
            // Check if the item exists
            $item = $this->get_item_by_id($id);
            if (!$item) {
                throw new DbException("Item with ID $id does not exist.");
            }

            // If count is 0 or less, remove the item from the cart
            if ($count <= 0) {
                return $this->remove_from_cart($id);
            }

            // Insert or update the cart item
            $stmt = $this->pdo->prepare(
                "INSERT INTO cart (id, count)
                VALUES (:id, :count)
                ON CONFLICT(id) DO UPDATE SET
                    count = excluded.count;"
            );
            return $stmt->execute(['id' => $id, 'count' => $count]);
        } catch (PDOException $e) {
            throw new DbException("Error adding/updating item in the cart.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Empty the cart
     *
     * @return bool
     * @throws DbException If there's a database error.
     */
    public function empty_cart(): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM cart");
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new DbException("Error removing item from the cart.\nCaused by: " . $e->getMessage());
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
            throw new DbException("Error removing item from the cart.\nCaused by: " . $e->getMessage());
        }
    }

    /**
     * Get item quantity in cart
     *
     * @param int $id
     * @return int The quantity of the item in the cart, or 0 if not found.
     * @throws DbException If there's a database error.
     */
    public function get_cart_item_quantity(int $id): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT count FROM cart WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetchColumn();
            return $result !== false ? (int)$result : 0;
        } catch (PDOException $e) {
            throw new DbException("Error getting cart item quantity.\nCaused by: " . $e->getMessage());
        }
    }
}
