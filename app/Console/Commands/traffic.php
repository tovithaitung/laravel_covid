<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TrafficBotController;
class traffic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traffic {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'traffic bot from gg';

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
    public function handle(TrafficBotController $traffic)
    {
        //
    	$type = $this->argument('type');
    	$type = (int)$type;
    	switch ($type) {
    		case 1:
    			$traffic->checkLive();
    			break;
    		case 2:
    			$traffic->visitFromGoogle('android call log symbols','milagromobilemarketing.com',false);
    			break;
            /*case 5:
                $traffic->checkIndex();
                break;*/
    		case 3:
    			$traffic->addProxy();
    			break;
    		case 4:
    			$traffic->requestIp();
    			break;
            case 5:
                $traffic->search();
                break;
    		default:
                echo 1;die;
    			$traffic->checkLive();
    			break;
    	}
        //$traffic->visitFromGoogle('android call log symbols','milagromobilemarketing.com');
        //$traffic->checkLive();
    }
}
