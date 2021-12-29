# Laravel log metrics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotburo/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-log-metrics)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-log-metrics/run-tests?label=tests)](https://github.com/dotburo/laravel-log-metrics/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-log-metrics/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dotburo/laravel-log-metrics/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dotburo/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-log-metrics)

**In development!** Simple Laravel tool to log messages and metrics to a database.

## Installation

You can install the package via composer:

```bash
composer require dotburo/laravel-log-metrics
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="dotburo\LogMetrics\LogMetricsServiceProvider" --tag="laravel-log-metrics-migrate"
php artisan migrate
```

## Logging trait examples

```php
use Dotburo\LogMetrics\Models\Metric;
use Dotburo\LogMetrics\Logging;
use Psr\Log\LogLevel;

class YourClass {
    use Logging;
    
    protected function yourMethod()
    {
        // Message examples
        
        $this->message()
            ->notice('Import process started')
            ->warn('Import process aborted')
            ->save();
        
        $this->message('Import process completed', LogLevel::WARNING)->save();

        $this->message('Import process completed', LogLevel::WARNING)->last()->save();
        
        
       
        // Metric examples
        
        $this->metric('density', 5)->save();
        
        $this->metrics([
            ['key' => 'density', 'value' => 5.3567],
            ['key' => 'pressure', 'value' => 2.35, 'unit' => 'bar', 'type' => 'int'],
            new Metric(['key' => 'quality', 'value' => 3])
        ]);
        
        $this->metrics()->setTenant(5)->setRelation($this->message()->last())->save();
    }
}
```

## Factory instantiation examples

```php
use Dotburo\LogMetrics\Factories\MessageFactory;
use Dotburo\LogMetrics\Factories\MetricFactory;
use Psr\Log\LogLevel;

$messageFactory = new MessageFactory();
$messageFactory->add('Import process completed', LogLevel::NOTICE);
$messageFactory->add('Bad air quality', LogLevel::WARNING);
$messageFactory->setTenant(5);
$messageFactory->setRelation($yourModel);
$messageFactory->setContext('Import process');
#$messageFactory->setLevel('key', LogLevel::ERROR);
#$messageFactory->setBody('key', 'Air quality import process completed');
$messageFactory->save(false);

$metricFactory = new MetricFactory();
$metricFactory->add('pressure', 2.35, 'bar', 'int');
$metricFactory->add('density', 5.43);
$metricFactory->setTenant(5);
$metricFactory->setContext('Import process');
$metricFactory->setRelation($messageFactory->last());
$metricFactory->last()->value = 5.45;
$metricFactory->save();
```

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
