<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\OnSportController;
class OnSport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onsport {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'onsport description';

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
    public function handle(OnSportController $onsport)
    {
        //

        $type = $this->argument('id');
        switch ($type) {
            case 1:
                // code...
                $onsport->categories();
                break;
            case 2:
                // code...
                $onsport->runPostCategory();
                break;
            case 3:
                // code...
                $onsport->detailPost();
                break;
            default:
                // code...
                $onsport->categories();
                break;
        }
    }
}
