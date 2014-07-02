<?php

use Bazo\Iban\IbanGenerator;
use Tester\Environment;
use Tester\Assert;

require_once '../vendor/autoload.php';

Environment::setup();

$number = 'insert your own';
$bankCode = 'insert your own';
$prefix = '';
$iban = 'insert your own';

$ibanGenerator = new IbanGenerator;

//fails
Assert::equal(FALSE, $ibanGenerator->generate('1234567', '1234', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('123456', '1', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('123456', '12345678901', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('', '1234', '123'));

//correct
Assert::equal($iban, $ibanGenerator->generate($prefix, $number, $bankCode));
