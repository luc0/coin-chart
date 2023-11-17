<?php

namespace App\Console\Commands;

use App\Services\CryptoService;
use Illuminate\Console\Command;

class syncGithubCommits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all crypto github commits';
    private CryptoService $cryptoService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CryptoService $cryptoService)
    {
        parent::__construct();
        $this->cryptoService = $cryptoService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $syncResult = $this->cryptoService->syncGithubData();

        if ($syncResult) {
            $this->line('Sync successful.');
            return;
        }

        $this->error('An error occur when syncing.');
    }
}
