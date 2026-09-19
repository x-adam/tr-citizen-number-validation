# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Minimum PHP version raised from 7.4 to 8.1.
- `validate()` now declares `int|string` for its argument and `bool` as its return type.
- Autoloading switched from classmap to PSR-4.

### Fixed

- Valid numbers whose intermediate checksum `(odd × 7) − even` is negative were rejected
  because PHP's `%` keeps the sign of the dividend. Roughly one in ten thousand valid
  numbers was affected (for example `17092819052`).

### Added

- PHPUnit test suite covering the checksum rules, malformed input and generated numbers.
- GitHub Actions workflow running the suite on PHP 8.1 through 8.5.
- MIT license file.

[Unreleased]: https://github.com/X-Adam/tr-citizen-number-validation/compare/4.x...5.x
