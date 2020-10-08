<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatesResource;
use App\Rate;
use Illuminate\Http\Request;

class RatesController extends Controller
{
    protected $api;
    protected $data;
    public $message;

    public function Lasting()
    {
        $this->transformData();
        $this->sendMessage('1253715500:AAEVWReTZtyPH-neec4lLHiUvhBEgiTomjg', $this->message);
        return $this->message;
    }

    public function History()
    {
        $this->data = Rate::query()->orderBy('date', 'DESC')->get();
        return RatesResource::collection($this->data);
    }

    private function transformData()
    {
        $this->Main();
        foreach ($this->data as $key => $value) {
            $this->message .= "$key = $value руб.  ";
        }
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
        $this->data = $this->SelectRates(['all']);
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

    private function sendMessage($token, $message)
    {
        $params = [
            'chat_id' => $_GET['id'],
            'text' => $message
        ];

        $URL  = 'https://api.telegram.org/bot' . $token . "/sendMessage?" . http_build_query($params);

        return json_decode(file_get_contents($URL),
            JSON_OBJECT_AS_ARRAY);
    }
}
