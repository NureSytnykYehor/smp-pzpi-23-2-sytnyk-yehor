<?php
class ItemsController
{
    private $db;
    private $cartRepo;

    public function __construct(DB $db)
    {
        $this->db = $db;
        $this->cartRepo = new CartRepository($db);
    }

    public function index(): void
    {
        $data = [
            'title' => 'Сторінка товарів',
            'items' => $this->db->get_items(),
            'cart_count' => $this->cartRepo->getCount(),
            'user' => $_SESSION['user']
        ];

        $this->render('items', $data);
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
