<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatesResource;
use App\Rate;
use Illuminate\Http\Request;

class RatesController extends Controller
{
    protected $api;
    protected $data;

    public function Lasting()
    {
        $this->Main();
        return $this->data;
    }

    public function History()
    {
        $this->data = Rate::query()->orderBy('date', 'DESC')->get();
        return RatesResource::collection($this->data);
    }

    private function Main()
    {
        $this->getApi();
        $this->getData();
        if (!$this->checkDate()) {
            $this->createToBase();
        }
    }

    private function getApi()
    {
        $api = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');
        $this->api = json_decode($api);
    }

    private function getData()
    {
        $this->data = $this->SelectRates(['USD', 'EUR']);
    }

    private function SelectRates(array $valutes)
    {
        $data = [];
        $rates = $this->api->Valute;
        if (in_array('all', $valutes)) {
            foreach ($rates as $name => $info) {
                $data[$name] = $info->Value;
            }
        } else {
            foreach ($valutes as $name) {
                $data[$name] = $rates->$name->Value;
            }
        }
        return $data;
    }

    private function createToBase()
    {
        Rate::query()->create(['data' => $this->data, 'date' => now()]);
    }

    private function checkDate()
    {
        return Rate::query()->where('date', date('Y-m-d'))->exists();
    }
}
