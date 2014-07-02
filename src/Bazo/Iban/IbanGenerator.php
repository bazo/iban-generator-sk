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
	const ACCOUNT_NUMBER_ERROR = 'Account number is not correct';
	const COUNTRY_CODE = 'SK';
	const COUNTRY_CODE_NUMBER = 2820; //SK

	private $prefixWeights = [
		1	 => 10,
		2	 => 5,
		3	 => 8,
		4	 => 4,
		5	 => 2,
		6	 => 1
	];
	private $numberWeights = [
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
	];

	public function __construct()
	{
		if (!extension_loaded('gmp')) {
			throw new \RuntimeException('gmp extension must be loaded');
		}
	}


	public function generate($prefix, $number, $bankCode)
	{
		try {
			$this->verifyPrefixLength($prefix);
			$this->verifyNumberLength($number);
			$this->verifyBankCodeLength($bankCode);
			$core = $this->generateCore($prefix, $number);
			$baseNumber = $this->generateBaseNumber($core, $bankCode);
			$controlCode = $this->generateControlCode($baseNumber);

			return self::COUNTRY_CODE . $controlCode . $bankCode . $core;
		} catch (IbanValidationException $ex) {
			return FALSE;
		}
	}


	private function generateControlCode($baseNumber)
	{
		$mod = gmp_mod($baseNumber, 97);
		$controlNumber = 98 - $mod;
		return str_pad($controlNumber, 2, '0', STR_PAD_LEFT);
	}


	private function generateBaseNumber($core, $bankCode)
	{
		return $bankCode . $core . self::COUNTRY_CODE_NUMBER . '00';
	}


	private function generateCore($prefix, $number)
	{
		$prefix = str_pad($prefix, 6, '0', STR_PAD_LEFT);
		$number = str_pad($number, 10, '0', STR_PAD_LEFT);
		$this->verifyCore($prefix, $number);
		return $prefix . $number;
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


}