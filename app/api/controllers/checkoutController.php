<?php
session_start();
require __DIR__ . '/../../Services/paymentService.php';
require_once __DIR__ . '/../../Models/paymentModel.php';

class CheckoutController{
    private $paymentService;

    function __construct(){
        $this->paymentService = new PaymentService();
    }

    function index(){
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($this->paymentService->getAll());
        }

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $paymentJsonString = file_get_contents('php://input');

            $paymentData = json_decode($paymentJsonString, true);

            $payment = new PaymentDetailsModel();

            $payment->setUserid($_SESSION["user_id"]);
            $payment->setFirstName($paymentData["first_name"]);
            $payment->setLastName($paymentData["last_name"]);
            $payment->setEmail($paymentData["email"]);
            $payment->setAddress($paymentData["address"]);
            $payment->setZip($paymentData["zip"]);
            $payment->setPhoneNumber($paymentData["phone_number"]);
            $payment->setPaymentMethod($paymentData["payment_method"]);
            $payment->setTotal($paymentData["total"]);

            $this->paymentService->insert($payment);
        }
    }
}
?>