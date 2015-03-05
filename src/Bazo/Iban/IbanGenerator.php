<?php

namespace Bazo\Iban;



/**
 * @author Martin Bažík <martin@bazo.sk>
 * @see http://www.web-zoznam.sk/2014/01/kalkulacka-prevodu-cisla-uctu-na-iban/
 */
class IbanGenerator
{

	const PREFIX_ERROR = 'Prefix cannot exceed 6 characters';
	const NUMBER_ERROR = 'Number length must be between 2 and 10 characters';
	const BANK_CODE_ERROR = 'Bank code must be between 4 characters';
    const BANK_CODE_STATE_ERROR = 'Not a valid Slovak bank code';
	const ACCOUNT_NUMBER_ERROR = 'Account number is not correct';
	const COUNTRY_CODE = 'SK';
	const COUNTRY_CODE_NUMBER = '2820'; //SK

	private $prefixWeights = array(
		1	 => 10,
		2	 => 5,
		3	 => 8,
		4	 => 4,
		5	 => 2,
		6	 => 1
    );
	private $numberWeights = array(
		1	 => 6,
		2	 => 3,
		3	 => 7,
		4	 => 9,
		5	 => 10,
		6	 => 5,
		7	 => 8,
		8	 => 4,
		9	 => 2,
		10	 => 1
    );
    private $bankAccountCodes = array(
        '0200',
        '0720',
        '0900',
        '1100',
        '1111',
        '3000',
        '3100',
        '5200',
        '5600',
        '5900',
        '6500',
        '7300',
        '7500',
        '7930',
        '8020',
        '8050',
        '8100',
        '8120',
        '8130',
        '8160',
        '8170',
        '8180',
        '8191',
        '8300',
        '8320',
        '8330',
        '8340',
        '8350',
        '8360',
        '8370',
        '8380',
        '8390',
        '8400',
        '8410',
        '8420',
        '8430',
        '9950',
        '9951',
        '9952'
    );

	public function __construct()
	{
		if (!extension_loaded('bcmath')) {
			throw new \RuntimeException('gmp extension must be loaded');
		}
	}


	public static function create()
	{
		return new static;
	}


	public function generate($prefix, $number, $bankCode)
	{
		try {
			$this->verifyPrefixLength($prefix);
			$this->verifyNumberLength($number);
			$this->verifyBankCodeLength($bankCode);
            $this->verifyBankCodeState($bankCode);
			$bban = $this->generateBban($bankCode, $prefix, $number);
			$baseNumber = $this->generateBaseNumber($bban);
			$controlCode = $this->generateControlCode($baseNumber);

			return self::COUNTRY_CODE . $controlCode . $bban;
		} catch (IbanValidationException $ex) {
			return FALSE;
		}
	}


	private function generateControlCode($baseNumber)
	{
		$mod = bcmod($baseNumber, '97');
		$controlNumber = 98 - $mod;
		return str_pad($controlNumber, 2, '0', STR_PAD_LEFT);
	}


	private function generateBaseNumber($bban)
	{
		return $bban . self::COUNTRY_CODE_NUMBER . '00';
	}


	private function generateBban($bankCode, $prefix, $number)
	{
		$bankCode = str_pad($bankCode, 4, '0', STR_PAD_LEFT);
		$prefix = str_pad($prefix, 6, '0', STR_PAD_LEFT);
		$number = str_pad($number, 10, '0', STR_PAD_LEFT);
		$this->verifyCore($prefix, $number);
		return $bankCode . $prefix . $number;
	}


	private function verifyCore($prefix, $number)
	{
		$sum = $this->sumPrefix($prefix) + $this->sumNumber($number);
		$mod = $sum % 11;

		if ($mod !== 0) {
			throw new IbanValidationException(self::PREFIX_ERROR);
		}
	}


	private function sumPrefix($prefix)
	{
		$sum = 0;

		for ($i = 1; $i <= 6; $i++) {
			$num = (int) $prefix[$i - 1];
			$weight = $this->prefixWeights[$i];
			$sum += $num * $weight;
		}

		return $sum;
	}


	private function sumNumber($number)
	{
		$sum = 0;

		for ($i = 1; $i <= 10; $i++) {
			$num = (int) $number[$i - 1];
			$weight = $this->numberWeights[$i];
			$sum += $num * $weight;
		}

		return $sum;
	}


	private function verifyPrefixLength($prefix)
	{
		$prefix = (string) $prefix;
		$length = strlen($prefix);

		if ($length > 6) {
			throw new IbanValidationException(self::PREFIX_ERROR);
		}
	}


	private function verifyNumberLength($number)
	{
		$number = (string) $number;
		$length = strlen($number);

		if ($length > 10 or $length < 2) {
			throw new IbanValidationException(self::NUMBER_ERROR);
		}
	}


	private function verifyBankCodeLength($bankCode)
	{
		$bankCode = (string) $bankCode;
		$length = strlen($bankCode);

		if ($length !== 4) {
			throw new IbanValidationException(self::BANK_CODE_ERROR);
		}
	}

    private function verifyBankCodeState($bankCode) {
        if(!in_array($bankCode,$this->bankAccountCodes)) {
            throw new IbanValidationException(self::BANK_CODE_STATE_ERROR);
        }
    }

}