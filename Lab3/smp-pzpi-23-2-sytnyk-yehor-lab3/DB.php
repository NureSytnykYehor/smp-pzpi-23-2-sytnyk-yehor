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
     * Retrieve user information from the database
     *
     * @return array
     * @throws DbException If there's a database error.
     */
    public function retrieve_user(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT name, age FROM settings;");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException("Error retrieving user info.\nCaused by: " . $e->getMessage());
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
