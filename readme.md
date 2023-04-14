# Molog for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-molog/run-tests?label=tests)](https://github.com/dotburo/laravel-molog/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dotburo/laravel-molog/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dotburo/laravel-molog/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)

Laravel Molog enables you to log messages and store metrics that are related to specific models. Akin to Spatie's 
[Activity Log](https://github.com/spatie/laravel-activitylog), but slightly more generic and with the possibility to
associate metrics (Gauges) to messages or to any other Laravel model.

In its simplest form:
```php
$user = auth()->user();

$this->message('Mail sent!')->concerning($user)->save();
```

A slightly more advanced example - a message with metrics for a custom model:
```php
$model = new Model();

$this->gauges()->startTimer();

$this->message()->notice('Import started...')->concerning($model)->save();

// processing...

$this->gauges()
    ->concerning($this->messageFactory()->last())
    ->gauge('Files accepted', 16)
    ->gauge('Files refused', 2)
    ->stopTimer()
    ->save();
```

Good old exception logging:
```php
$msg = $this->message(new Exception('Oops'))->setContext('example');

echo $msg;          // 2023-04-10 17:34:22.348 [debug] [example] Oops
echo $msg->body;    // Stack trace ...
```


## Usage
Install with composer:
```bash
composer require dotburo/laravel-molog
```

Publish the config file and migrations and migrate your app:
```bash
php artisan vendor:publish --provider="Dotburo\Molog\MologServiceProvider"

php artisan migrate
```

Wherever you need logging for a model, use the `Logging` trait:

```php
use Dotburo\Molog\Models\Gauge;
use Dotburo\Molog\Traits\Logging;
use Psr\Log\LogLevel;

class YourClass {
    use Logging;
    
    protected function handle()
    {
        // This will store three messages
        $this->messages()
            ->message('Import process initiated', LogLevel::INFO)
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
        
        // Associate all subsequent metrics with the last message
        $this->gauges()->concerning($this->messages()->last());
        
        // Associate this metric with the first message
        $this->gauge('density', 5)->concerning($this->messages()->first())->save();
        
        // Add three metrics associated with the last message
        $this->gauges([
            ['key' => 'density', 'value' => 5.3567],
            ['key' => 'pressure', 'value' => 2.35, 'unit' => 'bar', 'type' => 'int'],
            new Gauge(['key' => 'quality', 'value' => 3])
        ]);
        
        // This will save the four metrics
        $this->gauges()->save();
    }
}
```


## Documentation
Coming soon


## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [dotburo](https://github.com/dotburo)
- [All Contributors](../../contributors)

## License

GNU General Public License (GPL). Please see the [license file](LICENSE.md) for more information.
