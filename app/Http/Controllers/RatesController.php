<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatesController extends Controller
{
    protected $api;


    private function getApi()
    {
        $api = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');
        $this->api = json_decode($api);
    }

}
