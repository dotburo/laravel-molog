<?php

namespace Dotburo\Molog\Tests;

use Dotburo\Molog\Models\Message;
use Dotburo\Molog\Traits\Logging;
use Exception;
use Illuminate\Foundation\Auth\User;
use Psr\Log\LogLevel;
use Throwable;

class Logs
{
    use Logging;

    public function handle()
    {
        $user = new User();

        $user->id = 4;

        /** @var Message $message */
        $message = $this->message('Preparing to send message...', LogLevel::DEBUG);

        $message->concerning($user)
            ->setLevel(LogLevel::CRITICAL)
            ->setBody(new Exception('Sending error'))
            ->setContext('mailing')
            ->setTenant($user)
            ->setUser($user);

        $message->level = LogLevel::ERROR;
        $message->body = '';
        $message->tenant_id = 30;
        $message->user_id = 20;

        $message->save();

        return $message;
    }

    public function useCaseOne()
    {
        $message = $this->message('Function called, process started...')->setContext('uco-context');

        try {
            $results = [];
        } catch (Exception $exception) {
            $message->setLevel(LogLevel::ERROR)->setBody($exception)->save();

            return false;
        }

        if (empty($results)) {
            $message->warning('Process started, but did not yield results')->save();

            return false;
        }

        $message->save();

        $this->gaugeFactory()
            ->concerning($message)
            ->gauge('Result count', 25)
            ->gauge('Results accepted', 23)
            ->gauge('Results refused', 2)
            ->save();

        return true;
    }

    public function useCaseTwo()
    {
        $this->messageFactory()->setContext('UseCaseTwo');

        if (empty($something)) {
            $this->message()->error('Something\'s wrong, process aborted')->save();

            return 0;
        }

        $this->message()->debug('Starting process...');

        try {
            $results = collect();
        } catch (Throwable $e) {
            $this->message()->error('Process failed: ' . $e->getMessage());

            $this->messageFactory()->save();

            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $resultsCount = $results->count();

        if (!$resultsCount) {
            $this->message()->warning('No results');

            $this->messageFactory()->save();

            return 0;
        }

        $expectedCount = 10;

        $storedCount = 9;

        $message = $storedCount < $expectedCount
            ? $this->message()->warning("Some results were not imported")
            : $this->message()->info("All results imported");

        $message->save();

        $this->gaugeFactory()
            ->concerning($message)
            ->gauge('Results expected', $expectedCount)
            ->gauge('Results downloaded', $resultsCount)
            ->gauge('Results stored', $storedCount)
            ->save();

        return $storedCount;
    }

    public function useCaseThree()
    {
        $this->message()->log(LogLevel::NOTICE, 'Something is happening...')->save();

        $this->messageFactory()
            ->log(LogLevel::NOTICE, 'Something is happening...')
            ->log(LogLevel::NOTICE, 'Something is happening...')
            ->log(LogLevel::NOTICE, 'Something is happening...')
            ->save();

        $this->messageFactory()
            ->message(LogLevel::NOTICE, 'Something is happening...')
            ->message(LogLevel::NOTICE, 'Something is happening...')
            ->message(LogLevel::NOTICE, 'Something is happening...')
            ->save();

        $this->gaugeFactory()
            ->gauge('Result', 1)
            ->gauge('Result', 2)
            ->gauge('Result', 3)
            ->save();
    }
}
