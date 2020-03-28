<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CouponController;

class Coupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon {type=1}';

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
    public function handle(CouponController $coupon)
    {
        $type = $this->argument('type');
    	$type = (int)$type;
    	switch ($type) {
    		case 1:
    			$coupon->savehtml();
    			break;
    		case 2:
    			$coupon->InsertUrl();
    			break;
    		case 3:
    			$coupon->AddCouponHtml();
    			break;
		}
    }
}
