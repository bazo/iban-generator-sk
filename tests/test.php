<?php

use Bazo\Iban\IbanGenerator;
use Tester\Environment;
use Tester\Assert;

require_once '../vendor/autoload.php';

Environment::setup();

$number = '122334455';
$bankCode = '7500';
$bankCode2 = '0200';
$prefix = '19';

$ibanNoPrefix = 'SK9802000000000122334455';
$iban = 'SK0575000000190122334455';

$ibanGenerator = new IbanGenerator;

//fails
Assert::equal(FALSE, $ibanGenerator->generate('1234567', '1234', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('123456', '1', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('123456', '12345678901', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('', '1234', '123'));

//correct
Assert::equal($ibanNoPrefix, $ibanGenerator->generate('', $number, $bankCode2));
Assert::equal($iban, $ibanGenerator->generate($prefix, $number, $bankCode));