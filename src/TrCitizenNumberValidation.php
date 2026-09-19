<?php

namespace XAdam;

class TrCitizenNumberValidation
{
    public static function validate(int|string $citizenNumber): bool
    {
        $number = (string)$citizenNumber;

        if (!preg_match('/^[1-9][0-9]{9}[02468]$/', $number)) {
            return false;
        }

        $digits = array_map('intval', str_split($number));

        $odd = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
        $even = $digits[1] + $digits[3] + $digits[5] + $digits[7];

        if (((($odd * 7) - $even) % 10 + 10) % 10 !== $digits[9]) {
            return false;
        }

        return ($odd + $even + $digits[9]) % 10 === $digits[10];
    }
}
