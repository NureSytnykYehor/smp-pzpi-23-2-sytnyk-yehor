<?php
class CartRepository
{
    private $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function getItems(): array
    {
        return $this->db->get_cart();
    }

    public function getTotal(): float
    {
        return $this->db->get_cart_total();
    }

    public function getCount(): int
    {
        return $this->db->get_cart_count();
    }

    public function addItem(int $id, int $quantity): bool
    {
        return $this->db->add_to_cart($id, $quantity);
    }

    public function removeItem(int $id): bool
    {
        return $this->db->remove_from_cart($id);
    }

    public function clear(): bool
    {
        return $this->db->empty_cart();
    }

    public function getItemQuantity(int $id): int
    {
        return $this->db->get_cart_item_quantity($id);
    }
}
