<?php

use Bazo\Iban\IbanGenerator;
use Bazo\Iban\IbanValidationException;
use Tester\Environment;
use Tester\Assert;

require_once '../vendor/autoload.php';

Environment::setup();

$ibanGenerator = new IbanGenerator();

//fails
Assert::exception(function () use ($ibanGenerator) {
    $ibanGenerator->generate('1234567', '1234', '1234');
}, get_class(new IbanValidationException()), IbanGenerator::PREFIX_ERROR);
Assert::exception(function () use ($ibanGenerator) {
    $ibanGenerator->generate('123456', '1', '1234');
}, get_class(new IbanValidationException()), IbanGenerator::NUMBER_ERROR);
Assert::exception(function () use ($ibanGenerator) {
    $ibanGenerator->generate('123456', '12345678901', '1234');
}, get_class(new IbanValidationException()), IbanGenerator::NUMBER_ERROR);
Assert::exception(function () use ($ibanGenerator) {
    $ibanGenerator->generate('', '1234', '123');
}, get_class(new IbanValidationException()), IbanGenerator::BANK_CODE_ERROR);
Assert::exception(function () use ($ibanGenerator) {
    $ibanGenerator->generate('', '1234567890', '1234');
}, get_class(new IbanValidationException()), IbanGenerator::BANK_CODE_STATE_ERROR);

//correct
Assert::equal('SK0575000000190122334455', $ibanGenerator->generate(19, '122334455', '7500'));
Assert::equal('SK6202000000000122334455', $ibanGenerator->generate('', '122334455', '0200'));
Assert::equal('SK6480200000001100615760', $ibanGenerator->generate('', '1100615760', '8020'));