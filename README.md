# Laravel log metrics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotburo/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-log-metrics)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-log-metrics/run-tests?label=tests)](https://github.com/dotburo/laravel-log-metrics/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-log-metrics/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dotburo/laravel-log-metrics/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dotburo/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-log-metrics)

Simple Laravel tool to log messages and metrics to a database.

## Installation

You can install the package via composer:

```bash
composer require dotburo/laravel-log-metrics
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Dotburo\LogMetrics\LogMetricsServiceProvider" --tag="laravel-log-metrics-migrate"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Dotburo\LogMetrics\LogMetricsServiceProvider" --tag="laravel-log-metrics-config"
```

## Usage

* Use the trait in your models `use LogMetrics`
* [in progress]
*

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [dotburo](https://github.com/dotburo)
- [All Contributors](../../contributors)

## License

GNU General Public License (GPL). Please see the [license file](LICENSE.md) for more information.
