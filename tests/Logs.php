<?php

namespace Dotburo\Molog\Tests;

use Dotburo\Molog\Logging;
use Dotburo\Molog\Constants;
use Exception;

class Logs
{
    use Logging;

    public function handle()
    {
        $message = $this->message()
            ->notice('Sent')
            ->setContext('App\Mail\Mailable')
            ->setRelation(4, 'App\Models\User')
            ->last();

        $message->setLevelAttribute(Constants::ERROR)
            ->setBodyAttribute(new Exception('Sending error'));

        $this->message()->save();

        return $this->message();
    }

    /*
    public function handle()
    {
        $message = $this->message()
            ->notice('Sent')
            ->setContext('')
            ->setRelation(4, 'App\Models\User');
            //->save();
            //->last();

        $this->getMessageFactory()
            ->setContext() // set for all next
            ->setRelation() // set for all next
            ->save(); // saves all TODO: required to call if multiple messages

        $message->setLevelAttribute(Constants::ERROR)
            ->setBodyAttribute(new Exception('Sending error'));

        $message->setLevelAttribute();
        $message->setLevel();
        $message->level = '';


        $this->message()->save();

        return $this->message();
    } */
}
