<?php

use Bazo\Iban\IbanGenerator;
use Tester\Environment;
use Tester\Assert;

require_once '../vendor/autoload.php';

Environment::setup();

$ibanGenerator = new IbanGenerator;

//fails
Assert::equal(FALSE, $ibanGenerator->generate('1234567', '1234', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('123456', '1', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('123456', '12345678901', '1234'));
Assert::equal(FALSE, $ibanGenerator->generate('', '1234', '123'));

//correct
Assert::equal('SK0575000000190122334455', $ibanGenerator->generate(19, '122334455', '7500'));
Assert::equal('SK6202000000000122334455', $ibanGenerator->generate('', '122334455', '0200'));
Assert::equal('SK6480200000001100615760', $ibanGenerator->generate('', '1100615760', '8020'));