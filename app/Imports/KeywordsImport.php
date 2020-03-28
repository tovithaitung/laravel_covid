<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class KeywordsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        //

        foreach ($rows as $key => $row) {
        	if($key == 0){
        		continue;
        	}
        	print_r($row[2].PHP_EOL);
        }
    }
}
