<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AnswerPublicController;
class CheckKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Keywords description';

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
    public function handle(AnswerPublicController $keywords)
    {
        //

        $keywords->importTopPage();

    }
}
