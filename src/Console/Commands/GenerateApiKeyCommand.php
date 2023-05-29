<?php

namespace Mdigi\QrisBankJateng\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Mdigi\QrisBankJateng\Services\QrisService;

class GenerateApiKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qris:api-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate External Api Key';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(QrisService $service)
    {
        $this->info('Your encrypted api key: [' . $service->makeExternalApiKey() . ']. Save it to a safe place.');
        return Command::SUCCESS;
    }
}
