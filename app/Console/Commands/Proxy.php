<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ProxyController;
class Proxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy {type} {--min=*} {--max=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proxy crawl cmd';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ProxyController $proxy)
    {
        //
        $type = $this->argument('type');
        switch ($type) {
            case 'live':
                $min = $this->option('min');
                $max = $this->option('max');
                $proxy->liveProxy($min[0], $max[0]);
                break;
            case 'runlive':
                $proxy->runCheckLive();
                break;
            case 'crawl':
                $proxy->runCrawlProxy();
                break;
            default:
                // code...
                break;
        }
    }
}
