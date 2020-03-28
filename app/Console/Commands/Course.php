<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CourseController;

class Course extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'course:runauto {type=1} {limit=1}';
    // protected $signature = 'course:savehtml {type=1} {start=1} {end=1}';
    protected $signature = 'course:insert {type=1} {start=1} {end=1}';

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
    public function handle(CourseController $course)
    {
        $type = $this->argument('type');
        // $category_id = $this->argument('category_id');
		// $list_cate = array('268','328','294','292','296','269','290','274','273','276','278','300');
    	$type = (int)$type;
    	switch ($type) {
    		case 1:
				$start = $this->argument('start');
				$end = $this->argument('end');
    			$course->savehtml($start, $end);
    			break;
    		case 2:
    			$course->InsertUrl();
    			break;
    		case 3:
    			$course->getAPI();
    			break;
    		case 4:
    			$course->AutoFill();
    			break;
    		case 5:
				$start = $this->argument('start');
				$end = $this->argument('end');
    			$course->InsertCourse($start,$end);
    			break;
    		case 6:
				$limit = $this->argument('start');
    			$course->RunAuto($limit);
    			break;
    		case 7:
    			$course->InsertCourseSera();
    			break;
    		case 8:
    			$course->GetPageListEDX();
    			break;
    		case 9:
    			$course->GetProgramEDX();
    			break;
    		case 10:
    			$course->InsertCourseEDX();
    			break;
    		case 11:
    			$course->InsertCourseMaster();
    			break;
    		case 12:
    			$course->InsertPageList();
    			break;
    		case 13:
				$start = $this->argument('start');
				$end = $this->argument('end');
    			$course->InsertCourseLynda($start, $end);
    			break;
    		case 14:
    			$course->GetPageListEdin();
    			break;
		}
    }
}
