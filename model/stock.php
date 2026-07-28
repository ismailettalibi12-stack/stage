<?php
class stock {
    private $id;
    private $item_name;
    private $category;
    private $quantity;
    private $unit;
    private $min_quantity;
    private $expiry_date;
    private $supplier;
    private $unit_price;
    private $created_at;
    private $updated_at;

    public function __construct($id, $item_name, $category, $quantity, $unit, $min_quantity, $expiry_date, $supplier, $unit_price, $created_at, $updated_at) {
        $this->id = $id;
        $this->item_name = $item_name;
        $this->category = $category;
        $this->quantity = $quantity;
        $this->unit = $unit;
        $this->min_quantity = $min_quantity;
        $this->expiry_date = $expiry_date;
        $this->supplier = $supplier;
        $this->unit_price = $unit_price;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId() {
        return $this->id; 
    }

    public function getItemName() {
        return $this->item_name;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function getMinQuantity() {
        return $this->min_quantity;
    }

    public function getExpiryDate() {
        return $this->expiry_date;
    }

    public function getSupplier() {
        return $this->supplier;
    }

    public function getUnitPrice() {
        return $this->unit_price;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }
}
