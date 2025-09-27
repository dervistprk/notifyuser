<?php

namespace App\Console\Commands;

use App\Jobs\SendPendingMessagesJob;
use Illuminate\Console\Command;

class SendMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Veritabanındaki gönderilmemiş mesajların ilgili müşteri veya müşterilere gönderimini sağlar.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        SendPendingMessagesJob::dispatch();
        $this->info('Mesaj gönderim işlemi kuyruğa alındı.');
    }
}
