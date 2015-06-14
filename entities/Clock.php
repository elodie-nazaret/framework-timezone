<?php
namespace timezone\entities;

/**
 * Class Clock
 */
class Clock
{
    private $id;
    private $town;
    private $country;
    private $timezone;
    private $views = array();

    const TABLE_CLOCK   = 'horloge';
    const COL_ID        = 'id_horloge';
    const COL_TOWN      = 'ville_horloge';
    const COL_COUNTRY   = 'pays_horloge';
    const COL_TIMEZONE  = 'fuseau_horloge';

    /**
     * @param int $id
     * @param string $town
     */
    public function __construct($id, $town)
    {
        $this->id   = $id;
        $this->town = $town;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
        $country->addClock($this);
    }

    /**
         * @return Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param Timezone $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        $timezone->addClock($this);
    }

    /**
     * @return View[]
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param View[] $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @param View $view
     */
    public function addView(View $view)
    {
        $this->views[] = $view;
    }

    /**
     * @param View $view
     */
    public function removeView(View $view)
    {
        $id = array_search($view, $this->views);
        if ($id !== false) {
            array_splice($this->views, $id, 1);
        }
    }

    /**
     * @return string
     */
    public function toTile()
    {
//        $weather = WeatherApi()::getWeather($this->town);
//        $meteoIcon = $weather['weather'][0]['icon'];
//        $temperature = round($weather['main']['temp'] - 273.15, 1);

        $meteoIcon = '';
        $temperature = 10;

        return <<<TILE
<div class="clock col-xs-6 col-sm-4 clock-grid">
    <div class="clock-id hidden">{$this->id}</div>
    <div class="clock-city">{$this->town}</div>
    <div class="clock-country">{$this->country->getName()}</div>
    <div class="clock-date"></div>
    <div class="clock-timezone hidden">{$this->timezone->getName()}</div>
    <div class="clock-timezone-offset">{$this->timezone->getOffset()}</div>
    <div class="clock-clock">
        <svg class="clock-analog" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160" preserveAspectRatio="xMidYMid meet">
            <g>
                <circle r="78" cy="80" cx="80" stroke-width="4" stroke="#FFFFFF" fill="none"></circle>
                <g>
                    <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(30 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(60 80, 80)"></rect>
                    <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(90 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(120 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(150 80, 80)"></rect>
                    <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(180 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(210 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(240 80, 80)"></rect>
                    <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(270 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(300 80, 80)"></rect>
                    <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(330 80, 80)"></rect>
                </g>
                <g>
                    <rect height="75" width="1" y="70" x="79.5" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" class="second-hand"></rect>
                    <rect height="60" width="2" y="70" x="79" rx="2" ry="2" stroke="#FFFFFF" fill="#FFFFFF" class="minute-hand"></rect>
                    <rect height="45" width="3" y="70" x="78.5" rx="3" ry="3" stroke="#FFFFFF" fill="#FFFFFF" class="hour-hand"></rect>
                </g>
            </g>
        </svg>
        <div class="clock-digital"><span class="clock-digital-hour">10</span>:<span class="clock-digital-minute">15</span>:<span class="clock-digital-second">20</span></div>
        <div class="clock-ampm"></div>
        <div class="clock-weather"><img src="http://openweathermap.org/img/w/{$meteoIcon}.png" alt="Météo" title="Météo"/></div>
        <div class="clock-temp">{$temperature} °C</div>
    </div>
</div>
TILE;

    }

    /**
     * @return string
     */
    public function toSearchItem($isChecked)
    {
        $id = 'horloge_' . $this->id;
        $checked = $isChecked ? 'checked' : '';

        return <<<ITEM
<div class="col-xs-10">
    <label for="{$id}"><h4>{$this->country->getName()}, {$this->town}, {$this->timezone->getOffset()}</h4></label>
</div>
<div class="col-xs-2">
    <input type="checkbox" style="transform: scale(1.2); -webkit-transform: scale(1.2);" id="{$id}" name="{$this->id}" $checked>
</div>
ITEM;

    }
}