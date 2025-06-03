<?php
class HomeController
{
    private $cartRepo;

    public function __construct(DB $db)
    {
        $this->cartRepo = new CartRepository($db);
    }

    public function index(): void
    {
        $data = [
            'title' => 'Головна сторінка',
            'cart_count' => $this->cartRepo->getCount(),
            'user' => $_SESSION['user']
        ];

        $this->render('home', $data);
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
