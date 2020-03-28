<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ListCheckImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        //
        $list = array();
        foreach ($rows as $key => $row) {
        	$list[] = $row[0];
        }
        //print_r($list);
        //return $list;
    }
}
