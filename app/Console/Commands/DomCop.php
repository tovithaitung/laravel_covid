<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DomCopController;
class DomCop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domcop {bot}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DopCom CMD';

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
    public function handle(DomCopController $domcop)
    {
        //
        $type = $this->argument('bot');
        if($type == 1){
            $domcop->index(2);
        } else {
            $domcop->index();
        }
    }
}
