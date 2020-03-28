<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TagController;


class CommandTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag {type=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(TagController $tag)
    {
        $type = $this->argument('type');
		$type = (int)$type;
    	switch ($type) {
    		case 1:
    			$tag->run();
    			break;
    		case 2:
    			$tag->InsertKeyword();
    			break;
    		case 3:
    			$tag->AutoFill();
    			break;
		}
    }
}
