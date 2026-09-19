<?php

namespace XAdam\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use XAdam\TrCitizenNumberValidation;

class TrCitizenNumberValidationTest extends TestCase
{
    #[DataProvider('validNumbers')]
    public function testAcceptsValidNumbers(string $number): void
    {
        $this->assertTrue(TrCitizenNumberValidation::validate($number));
    }

    #[DataProvider('invalidNumbers')]
    public function testRejectsInvalidNumbers(string $number): void
    {
        $this->assertFalse(TrCitizenNumberValidation::validate($number));
    }

    public function testAcceptsIntegerInput(): void
    {
        $this->assertTrue(TrCitizenNumberValidation::validate(10000000146));
        $this->assertFalse(TrCitizenNumberValidation::validate(12345678901));
    }

    public function testAcceptsEveryGeneratedNumber(): void
    {
        foreach (self::generate(5000) as $number) {
            $this->assertTrue(
                TrCitizenNumberValidation::validate($number),
                "Generated valid number was rejected: {$number}",
            );
        }
    }

    public function testAcceptsNumbersWhereIntermediateChecksumIsNegative(): void
    {
        foreach (self::generateWithNegativeIntermediate(200) as $number) {
            $this->assertTrue(
                TrCitizenNumberValidation::validate($number),
                "Number with negative intermediate checksum was rejected: {$number}",
            );
        }
    }

    public function testRejectsNumbersWithBrokenChecksum(): void
    {
        foreach (self::generate(500) as $number) {
            $lastDigit = (int)$number[10];
            $broken = substr($number, 0, 10) . (($lastDigit + 2) % 10);

            $this->assertFalse(
                TrCitizenNumberValidation::validate($broken),
                "Number with broken checksum was accepted: {$broken}",
            );
        }
    }

    public static function validNumbers(): array
    {
        return [
            'lowest possible' => ['10000000146'],
            'repeating digits' => ['11111111110'],
            'negative intermediate checksum' => ['17092819052'],
        ];
    }

    public static function invalidNumbers(): array
    {
        return [
            'wrong checksum' => ['12345678901'],
            'wrong tenth digit' => ['10000000136'],
            'wrong eleventh digit' => ['10000000148'],
            'all zeros' => ['00000000000'],
            'leading zero' => ['01000000146'],
            'odd last digit' => ['10000000145'],
            'too short' => ['1000000014'],
            'too long' => ['100000001466'],
            'letters' => ['abcdefghijk'],
            'mixed' => ['1709281905a'],
            'empty' => [''],
            'trailing space' => ['17092819052 '],
            'leading space' => [' 17092819052'],
            'negative' => ['-7092819052'],
            'decimal' => ['1.7092819052'],
            'comma in last digit' => ['1709281905,'],
        ];
    }

    private static function generate(int $count): iterable
    {
        mt_srand(20260919);

        for ($i = 0; $i < $count; $i++) {
            $digits = [mt_rand(1, 9)];
            for ($d = 1; $d < 9; $d++) {
                $digits[] = mt_rand(0, 9);
            }

            yield self::complete($digits);
        }
    }

    private static function generateWithNegativeIntermediate(int $count): iterable
    {
        mt_srand(19920921);
        $produced = 0;

        while ($produced < $count) {
            $digits = [mt_rand(1, 3)];
            for ($d = 1; $d < 9; $d++) {
                $digits[] = $d % 2 === 1 ? mt_rand(7, 9) : mt_rand(0, 1);
            }

            $odd = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
            $even = $digits[1] + $digits[3] + $digits[5] + $digits[7];

            if (($odd * 7) - $even >= 0) {
                continue;
            }

            $produced++;

            yield self::complete($digits);
        }
    }

    private static function complete(array $digits): string
    {
        $odd = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
        $even = $digits[1] + $digits[3] + $digits[5] + $digits[7];

        $digits[] = ((($odd * 7) - $even) % 10 + 10) % 10;
        $digits[] = array_sum($digits) % 10;

        return implode('', $digits);
    }
}
