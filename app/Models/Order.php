<?php

class Order
{
    private int $order_id;
    private int $payment_id;
    private string $invoice_date;
    private string $invoice_number;
    private string $List_Restaurant_Product_id;
    private string $List_dance_Product_id;
    private string $List_Tour_Product_id;

    public function getOrderId(): int
    {
        return $this->order_id;
    }

    public function setOrderId(int $order_id): void
    {
        $this->order_id = $order_id;
    }

    public function getPaymentId(): int
    {
        return $this->payment_id;
    }

    public function setPaymentId(int $payment_id): void
    {
        $this->payment_id = $payment_id;
    }

    public function getInvoiceDate(): string
    {
        return $this->invoice_date;
    }


    public function setInvoiceDate(): void
    {
        $this->invoice_date = date("Y-m-d H:i:s");
    }


    public function getInvoiceNumber(): string
    {
        return $this->invoice_number;
    }

    public function setInvoiceNumber(): void
    {
        $this->invoice_number = generateInvoiceNumber();
    }

    public function getListRestaurantProductId(): string
    {
        return $this->List_Restaurant_Product_id;
    }


    public function setListRestaurantProductId(string $List_Restaurant_Product_id): void
    {
        $this->List_Restaurant_Product_id = $List_Restaurant_Product_id;
    }

    public function getListDanceProductId(): string
    {
        return $this->List_dance_Product_id;
    }

    public function setListDanceProductId(string $List_dance_Product_id): void
    {
        $this->List_dance_Product_id = $List_dance_Product_id;
    }

    public function getListTourProductId(): string
    {
        return $this->List_Tour_Product_id;
    }

    public function setListTourProductId(string $List_Tour_Product_id): void
    {
        $this->List_Tour_Product_id = $List_Tour_Product_id;
    }
    public function getListTourProductIdAsArray(): array{
        return explode(",", $this->List_Tour_Product_id);
    }
    public function getListDanceProductIdAsArray(): array{
        return explode(",", $this->List_dance_Product_id);
    }
    public function getListRestaurantProductIdAsArray(): array{
        return explode(",", $this->List_Restaurant_Product_id);
    }
    private function generateInvoiceNumber() {
        // Get the current year in two digits
        $year = date("y");

        // Get the current month in two digits
        $month = date("m");

        // Generate a random 3-digit number
        $random = rand(100, 999);

        // Combine the year, month, and random number to create the invoice number
        $invoice_number = "INV-" . $year . "-" . $month . "-" . $random;

        return $invoice_number;
    }
}