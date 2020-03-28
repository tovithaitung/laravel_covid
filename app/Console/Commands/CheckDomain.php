<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DomainController;
class CheckDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain {type} {--min=*} {--max=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Domain';

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
    public function handle(DomainController $domain)
    {
        //
        $type = $this->argument('type');
        switch ($type) {
            case 'code':
                $min = $this->option('min');
                $max = $this->option('max');
                $domain->runcheckStatusCode($min, $max);
                break;
            case 'domcop':
                $min = $this->option('min');
                $domain->checkDomCop($min);
                break;
            case 'premium':
                $min = $this->option('min');
                $domain->premiumDomain($min);
                break;
            case 'autoDomcop':
                $domain->autoDomcop();
                break;
            case 'auto':
                $domain->checkStatusCode();
                break;
            case 'expire':
                $min = $this->option('min');
                $max = $this->option('max');
                $domain->checkExpired($min, $max);
                break;
            case 'autoexpire':
                $domain->runcheckExpired();
                break;
            case 'wayback':
                $domain->checkWayback();
                break;
            case 'auction':
                $domain->checkDomainAuction();
                break;
            case 'process':
                $domain->processDataAuction();
                break;
            case 'subdomain':
                $domain->checkSubDomain();
                break;
            case 'import':
                $min = $this->option('min');
                $max = $this->option('max');
                $domain->importFileDomain($min, $max);
                break;
            case 'link':
                $domain->importFileLink();
                break;
            default:
                echo 'error param'.PHP_EOL;
                echo 'code --min:min_id --max:max_id (run check status code website)'.PHP_EOL;
                echo 'auto run all cmd check status code'.PHP_EOL;
                echo 'expire --min:min_id --max:max_id (run check price website)'.PHP_EOL;
                echo 'autoexpire (run all cmd check price website)'.PHP_EOL;
                echo 'wayback (check wayback link of website)'.PHP_EOL;
                echo 'subdomain (remove subdomain)'.PHP_EOL;
                break;
        }
        
    }
}
