<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatesController extends Controller
{
    protected $api;
    protected $data;


    private function getApi()
    {
        $api = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');
        $this->api = json_decode($api);
    }


    private function SelectData(array $valutes)
    {
        $data = [];
        $rates = $this->api->Valute;

        if ( in_array('all', $valutes) ) {
            // select all
            foreach ( $rates as $name => $info ) {
                $data[$name] = $info->Value;
            }
        } else {
            // select data
            foreach ( $valutes as $name ) {
                $data[$name] = $rates->$name->Value;
            }
        }

        return $data;
    }
}
