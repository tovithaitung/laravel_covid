<?php

namespace App\Console\Commands;

use App\Http\Controllers\KeyWordsPlanerController;
use Illuminate\Console\Command;

class CheckPlanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get key words planners by google';

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
    public function handle(KeyWordsPlanerController $keyWordsPlanerController)
    {
        //return $keyWordsPlanerController->getKeyWords($this->argument('mail'), $this->argument('pass'));
        $keyWordsPlanerController->checkKeyword(array());
    }
}
