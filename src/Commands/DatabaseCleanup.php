<?php

namespace Dotburo\Molog\Commands;

use Carbon\Carbon;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\Models\Gauge;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Generic command to delete database rows older than a given time.
 *
 * @copyright 2022 dotburo
 * @author dotburo <code@dotburo.org>
 */
class DatabaseCleanup extends Command
{
    /** @inheritDoc */
    protected $signature = 'molog:db:cleanup {datetime : Parsable datetime format}';

    /** @inheritDoc */
    protected $description = 'Delete records older than the given time';

    /**
     * Delete the old records.
     * @return int
     */
    public function handle(): int
    {
        $time = Carbon::parse($this->argument('datetime'));

        $this->info("Deleting messages and gauges older than {$time->toDateTimeString()}...");

        $messages = Message::query()
            ->where('created_at', '<=', $time)
            ->get();

        $messages->each(function (Message $message) {
            $message->gauges()->delete();
        });

        $count = Message::query()
            ->where('created_at', '<=', $time)
            ->delete();

        $count += Gauge::query()
            ->where('created_at', '<=', $time)
            ->delete();

        $this->info($count . ' ' . Str::plural('row') . ' deleted.');

        return 0;
    }
}
