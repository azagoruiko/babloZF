<?php

namespace Bablo\Service;

interface CurrencyService {
    public function setRate($cureencyId, $rate, $date);
    public function getRate($currencyId, $date);
}
