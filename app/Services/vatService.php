<?php
//Tudor Nosca (678549)
require __DIR__ . '/../Repositories/vatRepository.php';

class VatService{
    public function getVat(){
        $vatRepo = new VatRepository();

        $vat = $vatRepo->getVat();

        return $vat;
    }
}