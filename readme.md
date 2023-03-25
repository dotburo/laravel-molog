# Laravel Molog

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-molog/run-tests?label=tests)](https://github.com/dotburo/laravel-molog/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-molog/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dotburo/laravel-molog/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)

**In development!** Simple Laravel tool to log messages and metrics to a database.

## Installation

You can install the package via composer:

```bash
composer require dotburo/laravel-molog
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Dotburo\Molog\MologServiceProvider" --tag="laravel-molog-migrate"
php artisan migrate
```

## Logging trait examples

```php
use Dotburo\Molog\Models\Metric;
use Dotburo\Molog\Logging;
use Psr\Log\LogLevel;

class YourClass {
    use Logging;
    
    protected function yourMethod()
    {
        // Message examples
        
        $this->message('Import process initiated', LogLevel::INFO)
            ->notice('Import process ongoing')
            ->warn('Import process aborted')
            ->save();
        
       $this->message()
            ->setContextGlobally('Import process')
            ->notice('ongoing')
            ->warn('aborted')
            ->setLevel(LogLevel::CRITICAL)
            ->save();
        
        // Metric examples
        
        $this->metric('density', 5)->save();
        
        $this->metrics([
            ['key' => 'density', 'value' => 5.3567],
            ['key' => 'pressure', 'value' => 2.35, 'unit' => 'bar', 'type' => 'int'],
            new Metric(['key' => 'quality', 'value' => 3])
        ]);
        
        $this->metrics()
            ->setTenant(5)
            ->setRelation($this->message()->previous())
            ->save();
    }
}
```

## Factory instantiation examples

```php
use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Factories\MetricFactory;
use Psr\Log\LogLevel;

$messageFactory = new MessageFactory();
$messageFactory->setTenantGlobally(7);
$messageFactory->add('Import process completed', LogLevel::NOTICE);
$messageFactory->add('Bad air quality', LogLevel::WARNING);
$messageFactory->setTenant(5);
$messageFactory->setRelation($yourModel);
$messageFactory->setContext('Import process');
#$messageFactory->setLevel('key', LogLevel::ERROR);
#$messageFactory->setBody('key', 'Air quality import process completed');
$messageFactory->save();
$messageFactory->reset();

$metricFactory = new MetricFactory();
$metricFactory->setRelationGlobally($messageFactory->previous());
$metricFactory->add('pressure', 2.35, 'bar', 'int');
$metricFactory->add('density', 5.43);
$metricFactory->setContext('Import process');
$metricFactory->previous()->value = 5.45;
$metricFactory->save();
$metricFactory->reset();

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
