<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Queue;

class QueueSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:size'; //@phpstan-ignore-line

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Showing size of the default queue'; //@phpstan-ignore-line

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->output->text('default queue size: ' . Queue::size('default') . PHP_EOL);
        return 0;
    }
}
