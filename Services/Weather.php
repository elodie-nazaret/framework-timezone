<?php

namespace Timezone\Services;

/**
 * Class Weather
 */
class Weather
{
    const APPID        = '87ebbac3eaa1d68a0e59a741fc5ef5c3';
    const BASE_API     = 'http://api.openweathermap.org/data/2.5/weather';
    const BASE_API_IMG = 'http://openweathermap.org/img/w/';

    private $city;
    private $country;
    private $weather;

    /**
     * @param string $city
     * @param string $country
     */
    function __construct($city, $country)
    {
        $this->city     = $city;
        $this->country  = $country;

        try {
            $weather = @file_get_contents(self::BASE_API . '?q=' . $city . ',' . $country . '&APPID=' . self::APPID, true);
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
        $weather = json_decode($weather, true);

        if (isset($weather['message']) && $weather['cod'] == '404') {
            $this->weather = null;
        } else {
            $this->weather = $weather;
        }
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        if ($this->getWeather())
            return '<img src="' . self::BASE_API_IMG . $this->weather['weather'][0]['icon'] . '.png" alt="Météo" title="Météo"/>';
    }

    /**
     * @return string
     */
    public function getCurrentTemp()
    {
        if ($this->getWeather())
            return round($this->weather['main']['temp'] - 273.15, 1) . ' °C';
    }

    /**
     * @return string
     */
    public function getHumidity()
    {
        if ($this->getWeather())
            return $this->weather['main']['humidity'] . '%';
    }

    /**
     * @return string
     */
    public function getPressure()
    {
        if ($this->getWeather())
            return $this->weather['main']['pressure'] . ' hPa';
    }

    /**
     * @return string
     */
    public function getMinTemp()
    {
        if ($this->getWeather())
            return round($this->weather['main']['temp_min'] - 273.15, 1) . ' °C';
    }

    /**
     * @return string
     */
    public function getMaxTemp()
    {
        if ($this->getWeather())
            return round($this->weather['main']['temp_max'] - 273.15, 1) . ' °C';
    }

    /**
     * @return string
     */
    public function getWind()
    {
        if ($this->getWeather())
            return round($this->weather['wind']['speed'] * 3.6, 2) . ' km/h';
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return array
     */
    public function getWeather()
    {
        return $this->weather;
    }
}
