<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AhrefController;
class CheckAhref extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ahref {type} {bot}';

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
    public function handle(AhrefController $ahref)
    {
        
        //$ahref->loginAhref();
        $bot = $this->argument('bot');
        $type = $this->argument('type');
        //$ahref->runCheck($bot);
        if($type == 'domain'){
            $ahref->runDomain();
        } elseif($type == 'final'){
            if($bot == 1){
                $ahref->checkBackLink();
            } elseif($bot == 2) {
                $ahref->checkRefferDomain();
            } elseif($bot == 3){
                $ahref->checkSEDomain();
            } elseif($bot == 4){
                $ahref->checkBackLinkDomCop();
            } elseif($bot ==5){
                $ahref->checkRefferDomainDomCop();
            } elseif($bot ==6){
                $ahref->checkSEDomainDomCop();
            } elseif($bot ==7){
                $ahref->checkPEDomain();
            } elseif($bot ==8){
                $ahref->checkAll();
            }
        } elseif($type == 'keyword') {
            $filter = array(
                'volume' => '100-0',
                'kd' => '0-1',
                'word_count' => '4-6',
                'keyword' => 'apk',
                'country' => $bot
            );
            //$ahref->runExtractKeyword('apkmirror.com',$filter);
            $ahref->keyWordFromExcel();
        } else {
            $filter = array(
                'volume' => '100-0',
                'kd' => '0-1',
                'word_count' => '4-6',
                'keyword' => 'android',
                'country' => $bot
            );
            $ahref->runCheck(1);
        }
        //$ahref->checkKeyword("download apk");
       // $ahref->runCheck();
    }
}
