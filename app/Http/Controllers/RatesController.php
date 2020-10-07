<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatesResource;
use App\Rate;
use Illuminate\Http\Request;

class RatesController extends Controller
{
    protected $api;          // API
    protected $data;          // Данные о валютах


    public function Lasting()          // Последние валюты
    {
        $this->Main();          // Главный
        return $this->data;          // Возвращает данные
    }


    public function History()          // История валют
    {
        $this->data = Rate::query()          // Данные это все данные из БД,
            ->orderBy('date', 'DESC')          // отсортированные по дате
            ->get();

        return RatesResource::collection($this->data);          // Возвращает данные
    }


    private function Main()          // Главный
    {
        $this->getApi();          // Получили API
        $this->getData();          // Получили данные о валютах

        if (!$this->checkDate()) {          // Если проверка не прошла, то

            $this->createToBase();          // создаем в базе
        }
    }


    private function getApi()          // Получаем API
    {
        $api = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');          // API это данные из ссылки...
        $this->api = json_decode($api);          // Декодируем данные из ссылки (API)
    }


    private function getData()          // Получаем данные о валютах
    {
        $this->data = $this          // Данные о валютах это данные отобранные как,
            ->SelectRates(['USD', 'EUR']);          // (USD - доллар, EUR - евро)
    }


    private function SelectRates(array $valutes)          // Выбор валют (...валюты...)
    {
        $data = [];          // Данные о выбранных валют пусты
        $rates = $this->api->Valute;          // Валюты это валюты из декодируемых данных из ссылки (API)

        if (in_array('all', $valutes)) {          // Если в валютах указан "all"
            // select all
            foreach ($rates as $name => $info) {          // Валюты как имя => информация о валюте
                $data[$name] = $info->Value;          // В данные о выбранных валютах записывается имя и значение валюты (ВСЕ)
            }
        } else {          // Иначе
            // select data
            foreach ($valutes as $name) {          // Валюты которые были выбраны как имя
                $data[$name] = $rates->$name->Value;          // В данные о выбранных валютах помещают имя (Как ввел пользователь) и значение этой валюты
            }
        }

        return $data;          // Возвращает данные о выбранных валютах
    }


    private function createToBase()          // Создание в базе
    {
        Rate::query()->create(          // Валюта -> создать ->
            [
                'data' => $this->data,          // Данные в базе это данные о валютах
                'date' => now()          // Дата это сегодняшняя дата
            ]
        );
    }

    private function checkDate()          // Проверка даты
    {
        return Rate::query()          // Возвращает Да если в дате есть сегодняшняя дата
            ->where('date', date('Y-m-d'))
            ->exists();
    }
}
