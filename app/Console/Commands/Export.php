<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AllInTitleController;
class Export extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exportappid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export appid';

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
    public function handle(AllInTitleController $export)
    {
        //
        $export->exportAppid();

    }
}
