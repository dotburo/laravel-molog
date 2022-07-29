<?php

namespace Dotburo\LogMetrics\Commands;

use Carbon\Carbon;
use Dotburo\LogMetrics\Models\Message;
use Dotburo\LogMetrics\Models\Metric;
use Illuminate\Console\Command;

/**
 * Generic command to delete database rows older than a given time.
 *
 * @copyright 2022 dotburo
 * @author dotburo <code@dotburo.org>
 */
class DatabaseCleanup extends Command
{
    /** @inheritDoc */
    protected $signature = 'log-metrics:database:cleanup {datetime : Parsable datetime format}';

    /** @inheritDoc */
    protected $description = 'Delete records older than the given time';

    /**
     * @todo Improve performance ?
     * @return void
     */
    public function handle(): void
    {
        $time = Carbon::parse($this->argument('datetime'));

        $this->info("Deleting messages and metrics older than {$time->toDateTimeString()}...");

        $messages = Message::query()
            ->where('created_at', '<=', $time)
            ->get();

        $messages->each(function (Message $message) {
            $message->metrics()->delete();
        });

        Message::query()
            ->where('created_at', '<=', $time)
            ->delete();

        Metric::query()
            ->where('created_at', '<=', $time)
            ->delete();

        $this->info('Done!');
    }
}
