<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllInTitleController;
use Illuminate\Console\Command;

class CheckTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'titles {offset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check All Title Google Seacrch description';

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
    public function handle(AllInTitleController $titles)
    {
        $offset = $this->argument('offset');
        if($offset == 1){
            return $titles->runSearch($offset);
        }elseif($offset == 2){
            return $titles->insertKey();
        } elseif($offset == 3){
            return $titles->insertKeyword();
        } elseif($offset == 4){
            return $titles->exportAppid();
        }  else {
            return $titles->runcheckIndex();
        }
    }
}
