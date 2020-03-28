<?php

namespace App\Console\Commands;

use App\Http\Controllers\HomeController;
use Illuminate\Console\Command;

class JoinDomainTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:join';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'join 2 table';

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
    public function handle(HomeController $controller)
    {
        return $controller->joinDomainRequestToDomainTemp();
    }
}
