# Molog for Laravel

[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/dotburo/laravel-molog/run-tests.yml?branch=main&label=Tests&style=flat-square)](https://github.com/dotburo/laravel-molog/actions/workflows/run-tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotburo/laravel-molog.svg?style=flat-square&label=Version)](https://packagist.org/packages/dotburo/laravel-molog)
[![Total Downloads](https://img.shields.io/packagist/dt/dotburo/laravel-molog.svg?style=flat-square)](https://packagist.org/packages/dotburo/laravel-molog)

Laravel Molog enables you to log messages and store metrics that are related to specific models. Akin to Spatie's 
[Activity Log](https://github.com/spatie/laravel-activitylog), but slightly more generic and with the possibility to
associate metrics (Gauges) to messages or to any other Laravel model. The gauges factory class also provides builtin timer 
and incrementation methods.

In its simplest form:
```php
$user = auth()->user();

$this->message('Mail sent!')->concerning($user)->save();
```

A slightly more advanced example &mdash; a message with metrics for a custom model:
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

echo $msg;              // 2023-04-10 17:34:22.348 [debug] [example] Oops
echo $msg->subject;     // Oops
echo $msg->body;        // Stack trace ...
```

## Features
- Attach log messages and metrics (gauges) to models
- Follows the PSR-3: Logger Interface
- Exception logging
- Start & stop timer metrics
- Increment and decrement methods for gauges
- Output messages and metrics to string
- Generic HTTP controller

## Usage
Install with composer from [packagist.org](https://packagist.org/packages/dotburo/laravel-molog):
```bash
composer require dotburo/laravel-molog
```

Publish the config file and migrations and migrate your app:
```bash
php artisan vendor:publish --provider="Dotburo\Molog\MologServiceProvider"

php artisan migrate
```

Wherever you need logging, use the `Logging` trait. See the documentation for more [usage examples](./doc/Examples.md).
```php
class YourClass {

    use \Dotburo\Molog\Traits\Logging;
    
    protected function handle()
    {
        // This will store three messages
        $this->messages()
            ->message('Import process initiated', \Psr\Log\LogLevel::INFO)
            ->notice('Import process ongoing')
            ->warn('Import process aborted')
            ->save();
        
       // This will store one message with the subject 'aborted' and level critical
       $this->message()
            ->setContext('Import process')
            ->notice('ongoing')
            ->warn('aborted')
            ->setLevel(\Dotburo\Molog\MologConstants::CRITICAL)
            ->save();
        
        // Associate all subsequent metrics with the last message
        $this->gauges()->concerning($this->messages()->last());
        
        // Associate this metric of type INT with the first message
        $this->gauge('density', 5)->concerning($this->messages()->first())->save();
        
        // Add three metrics associated with the last message
        $this->gauges()
            ->gauge('density', 5.3567)  // updates the previous 'density' metric to the FLOAT value
            ->gauge('pressure', 2.35, 'bar', \Dotburo\Molog\MologConstants::GAUGE_INT_TYPE) // forcibly cast to FLOAT
            ->gauge('quality', 3)
            ->save();
    }
}
```


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
