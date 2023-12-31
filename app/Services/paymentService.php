<?php
//Tudor Nosca (678549)
require_once __DIR__ . '/../Repositories/paymentRepository.php';

class PaymentService{
    private $paymentRepo;

    public function __construct(){
        $this->paymentRepo = new PaymentRepository();
    }

    public function getAll(){
        $payments = $this->paymentRepo->getAll();

        return $payments;
    }
    //Ale
    public function getByPaymentId($id)
    {
        $payment = $this->paymentRepo->getByPaymentId($id);

        return $payment;
    }

    public function getByUserId($user_id){
        $payment = $this->paymentRepo->getByUserId($user_id);

        return $payment;
    }

    public function addPaymentId($user_id, $payment_id){
        $this->paymentRepo->addPaymentId($user_id, $payment_id);
    }

    public function insert($payment){
        $this->paymentRepo->insert($payment);
    }
}
?>