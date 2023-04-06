# Laravel Molog

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-molog/run-tests?label=tests)](https://github.com/dotburo/laravel-molog/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-molog/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dotburo/laravel-molog/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)

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
use Dotburo\Molog\Models\Gauge;
use Dotburo\Molog\Traits\Logging;
use Psr\Log\LogLevel;

class YourClass {
    use Logging;
    
    protected function yourMethod()
    {
        // Message logging examples
        
        // This will store three messages
        $this->messageFactory()
            ->message(LogLevel::INFO, 'Import process initiated')
            ->notice('Import process ongoing')
            ->warn('Import process aborted')
            ->save();
        
       // This will log one message with the subject 'aborted' and level critical
       $this->message()
            ->setContext('Import process')
            ->notice('ongoing')
            ->warn('aborted')
            ->setLevel(LogLevel::CRITICAL)
            ->save();
        
        // Gauge examples
        
        $this->gaugeFactory()->concerning($this->messageFactory()->last());
        
        $this->gauge('density', 5)->concerning($this->messageFactory()->first())->save();
        
        $this->gauges([
            ['key' => 'density', 'value' => 5.3567],
            ['key' => 'pressure', 'value' => 2.35, 'unit' => 'bar', 'type' => 'int'],
            new Gauge(['key' => 'quality', 'value' => 3])
        ]);
        
        $this->gaugeFactory()->save();
    }
}
```

## Factory instantiation examples

```php
use Dotburo\Molog\Factories\MessageFactory;
use Dotburo\Molog\Factories\GaugeFactory;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LogLevel;

$model = Model::first();

$messageFactory = new MessageFactory();
$messageFactory->setTenant(7);
$messageFactory->message('Import process completed', LogLevel::NOTICE);
$messageFactory->setTenant(5);
$messageFactory->concerning($model);
$messageFactory->setContext('Import process');
$messageFactory->message('Bad air quality', LogLevel::WARNING);
$messageFactory->save();

$gaugeFactory = new GaugeFactory();
$gaugeFactory->concerning($messageFactory->last());
$gaugeFactory->setContext('Import process');
$gaugeFactory->gauge('pressure', 2.35, 'bar', 'int');
$gaugeFactory->gauge('density', 5.43);
$gaugeFactory->last()->value = 5.45;
$gaugeFactory->save();

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
