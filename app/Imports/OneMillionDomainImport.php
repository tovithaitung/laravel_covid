<?php

namespace App\Imports;

use App\DomainRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class OneMillionDomainImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @throws \Exception
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($key < 1000) {
                print_r($row[1].PHP_EOL);
                DomainRequest::updateOrCreate(
                    ['domain' => $row[1]],
                    [
                        'domain' => $row[1],
                        'status'=> 0,
                        'created_at' => new \DateTime(),
                    ]
                );
            }
        }
    }
}
