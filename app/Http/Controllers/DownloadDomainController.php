<?php

namespace App\Http\Controllers;

use App\Imports\OneMillionDomainImport;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DownloadDomainController extends Controller
{
    /**
     * @return $this
     */
    public  function downloadDomain()
    {
        $url = 'http://s3.amazonaws.com/alexa-static/top-1m.csv.zip';
        $path = public_path('oneMillonDomain\domain.zip');
        downloadFile($url, $path);
        return $this;
    }

    /**
     * @return $this
     */
    public function extractFile()
    {
        $path = public_path('oneMillonDomain\domain.zip');
        Zipper::make($path)->extractTo(public_path('oneMillonDomain'));
        return $this;
    }

    /**
     * @return \Maatwebsite\Excel\Excel
     */
    public function importDataToDomainRequestTempTable()
    {
        return Excel::import(new OneMillionDomainImport(), public_path('oneMillonDomain\top-1m.csv'));
    }

    /**
     * @return \Maatwebsite\Excel\Excel
     */
    public function execute()
    {
        return $this->downloadDomain()->extractFile()->importDataToDomainRequestTempTable();
    }
}
