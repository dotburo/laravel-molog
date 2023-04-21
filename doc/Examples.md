# Laravel Molog usage examples

## One log message
A unique message instance is updated as the process goes. 
If the process ran without errors it will results in a message with level INFO and three associated metrics.
```php
class YourClass
{
    use \Dotburo\Molog\Traits\Logging;

    public function handle()
    {
        /** @var \Dotburo\Molog\Models\Message $message */
        $message = $this->message('Process started...')->setContext('my-context');

        try {
            $results = [];
        } catch (Throwable $exception) {
            $message->error($exception)->save();

            return false;
        }

        if (empty($results)) {
            $message->warning('Process ran, but did not yield results')->save();

            return false;
        }

        $message->info('Process successful')->save();

        $this->gauges()
            ->concerning($message)
            ->gauge('Result count', 25)
            ->gauge('Results accepted', 23)
            ->gauge('Results refused', 2)
            ->save();

        return true;
    }
}
```

## Multiple log messages
A log message is created at each step of the process.
```php
class YourClass
{
    use \Dotburo\Molog\Traits\Logging;

    public function handle($condition)
    {
        # All subsequent messages will have this context
        $this->messages()->setContext('my-context');

        if (empty($condition)) {
            $this->message()->error('Condition is not valid, process aborted')->save();

            return 0;
        }

        # Default debug level
        $this->message('Starting process...');

        try {
            $results = collect();
        } catch (Throwable $e) {
            # Adds an error message and saves all previous messages
            $this->messages()->error($e)->save();

            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $resultsCount = $results->count();

        if (! $resultsCount) {
            # Adds an error message and saves all previous messages
            $this->messages()->warning('No results')->save();

            return 0;
        }

        $expectedCount = 10;
        $storedCount = 9;

        $message = $storedCount < $expectedCount
            ? $this->message()->warning('Some results were not imported')
            : $this->message()->info('All results imported');

        # Saves all previous messages
        $this->messages()->save();

        $this->gauges()
            ->concerning($message)
            ->gauge('Results expected', $expectedCount)
            ->gauge('Results downloaded', $resultsCount)
            ->gauge('Results stored', $storedCount)
            ->save();

        return $storedCount;
    }
}
```

## Timer and incrementation
```php
class YourClass
{
    use \Dotburo\Molog\Traits\Logging;

    public function handle()
    {
        $this->message()->debug('Starting process...');

        try {
            $this->gauges()->startTimer('process duration');

            foreach ($items as $item) {
                // process item
                $this->gauges()->increment('items processed');
            }

            $this->gauges()->stopTimer('process duration')->save();
        } catch (Throwable $e) {
            # Adds an error message and saves all previous messages
            $this->messages()->error($e)->save();
            
            # Attach all gauges to the last message & save all gauges
            $this->gauges()->concerning($this->messages()->last())->save();
            
            # Return number of processed items
            return $this->gauges()->firstWHere('key', 'items processed')->value;
        }

        $this->message()->info('Process result successful');

        # Attach all gauges to the last message & save all gauges
        $this->gauges()->concerning($this->messages()->last())->save();

        # Save all previous messages
        $this->messages()->save();

        # Return number of processed items
        return $this->gauges()->firstWHere('key', 'items processed')->value;
    }
}
```
