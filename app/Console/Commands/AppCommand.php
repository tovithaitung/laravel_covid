<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AppController;
class AppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app {offset}';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'App check';


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
    public function handle(AppController $app)
    {
        //
        $type = $this->argument('offset');
        switch ($type) {
            case 1:
                $app->relatePlanner();
                break;
            case 2:
                $app->getRelateApp();
                break;
            case 3:
                $app->kwplanerRelateApp();
                break;
            case 4:
                $app->checkSuggest();
                break;
            case 5:
                $app->checkApp();
                break;
            case 6:
                $app->checkKeyAppTop();
                break;
            case 7:
                $app->relateApp();
                break;

            case 8:
                $app->getAppFromAhref();
                break;
            case 9:
                $app->loginGG();
                break;
            case 10:
                $app->kwplanerKeyword();
                break;
            default:
                $app->relatePlanner();
                break;
        }
        //$app->relatePlanner();
    }
}
