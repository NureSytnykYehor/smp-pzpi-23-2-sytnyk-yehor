<?php
class CartController
{
    private $cartRepo;

    public function __construct(DB $db)
    {
        $this->cartRepo = new CartRepository($db);
    }

    public function index(): void
    {
        $data = [
            'title' => 'Кошик',
            'cart_items' => $this->cartRepo->getItems(),
            'cart_total' => $this->cartRepo->getTotal(),
            'cart_count' => $this->cartRepo->getCount(),
            'user' => $_SESSION['user']
        ];

        $this->render('cart', $data);
    }

    public function add(): void
    {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($product_id !== false && $product_id !== null && $quantity !== false && $quantity !== null) {
            try {
                $this->cartRepo->addItem($product_id, $quantity);
            } catch (DbException $e) {
                error_log("Cart handling error: " . $e->getMessage());
            }
        }

        header('Location: ?page=items');
        exit();
    }

    public function remove(): void
    {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

        if ($product_id !== false && $product_id !== null) {
            try {
                $this->cartRepo->removeItem($product_id);
            } catch (DbException $e) {
                error_log("Cart handling error: " . $e->getMessage());
            }
        }

        header('Location: ?page=cart');
        exit();
    }

    public function clear(): void
    {
        try {
            $this->cartRepo->clear();
        } catch (DbException $e) {
            error_log("Cart handling error: " . $e->getMessage());
        }

        header('Location: ?page=cart');
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
