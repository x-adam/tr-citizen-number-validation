# Turkish Citizen Number Validation

<p align="center">
<a href="https://github.com/X-Adam/tr-citizen-number-validation/actions/workflows/tests.yml"><img src="https://github.com/X-Adam/tr-citizen-number-validation/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
<a href="https://packagist.org/packages/x-adam/tr-citizen-number-validation" rel="nofollow"><img src="https://img.shields.io/packagist/v/x-adam/tr-citizen-number-validation" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/x-adam/tr-citizen-number-validation" rel="nofollow"><img src="https://img.shields.io/packagist/php-v/x-adam/tr-citizen-number-validation" alt="PHP Version"></a>
<a href="https://packagist.org/packages/x-adam/tr-citizen-number-validation" rel="nofollow"><img src="https://img.shields.io/packagist/dt/x-adam/tr-citizen-number-validation" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/x-adam/tr-citizen-number-validation" rel="nofollow"><img src="https://img.shields.io/packagist/l/x-adam/tr-citizen-number-validation" alt="License"></a>
</p>

Offline validation for Turkish citizen numbers (T.C. Kimlik No). Zero dependencies, a single static
method, and no network access — the number is checked against the official checksum rules.

## Requirements

PHP 8.1 or higher. Nothing else.

## Installation

```bash
composer require x-adam/tr-citizen-number-validation
```

## Usage

```php
use XAdam\TrCitizenNumberValidation;

TrCitizenNumberValidation::validate(10000000146);   // true
TrCitizenNumberValidation::validate('10000000146'); // true
TrCitizenNumberValidation::validate('12345678901'); // false
```

Both `int` and `string` are accepted, so values coming straight from a request need no casting or
pre-filtering. Anything that is not a well-formed citizen number — letters, empty strings,
surrounding whitespace, wrong length — returns `false` instead of throwing:

```php
TrCitizenNumberValidation::validate('not a number'); // false
TrCitizenNumberValidation::validate('');             // false
TrCitizenNumberValidation::validate('1709281905');   // false (10 digits)
```

## What it checks

A Turkish citizen number is 11 digits long and carries two check digits:

| Rule | Description |
|------|-------------|
| Length and range | Exactly 11 digits, the first one cannot be `0` |
| 10th digit | `((d1 + d3 + d5 + d7 + d9) × 7 − (d2 + d4 + d6 + d8)) mod 10` |
| 11th digit | `(d1 + … + d10) mod 10` |

Because the 11th digit is always the result of `8 × (d1 + d3 + d5 + d7 + d9) mod 10`, every valid
number ends with an even digit — the library rejects odd endings up front.

## What it does not check

This library answers **"is this a well-formed citizen number?"**, not **"does this number belong to
this person?"**. The latter requires querying the Identity Sharing System (KPS) of the Turkish
Directorate of Population and Citizenship Affairs; the formerly public NVİ endpoint was shut down on
30 September 2025, and verification against official records is now only available to institutions
with a KPS membership.

Use this library to reject malformed input early — before storing it, or before paying for a lookup.

## Laravel

For a ready-made `tr_citizen_number` validation rule, see
[x-laravel/validation-tr-citizen-number-extend](https://packagist.org/packages/x-laravel/validation-tr-citizen-number-extend),
which wraps this library.

## Testing

```bash
composer install
composer test
```

The suite covers the documented rules, malformed input, generated valid numbers, and numbers whose
intermediate checksum is negative. It runs on PHP 8.1 through 8.5 in CI.

## License

Open source software licensed under the [MIT license](LICENSE).
