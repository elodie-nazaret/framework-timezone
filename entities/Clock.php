<?php

namespace Timezone\Entities;

use Framework\Entities\Entity;
use Framework\HtmlTemplate;
use Timezone\Services\Weather;

/**
 * Class Clock
 */
class Clock extends Entity
{
    private $id;
    private $town;
    /* @var Country $country */
    private $country;
    /* @var Timezone $timezone */
    private $timezone;
    /* @var View[] $views */
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
     * Transforms the clock to a tile that is displayed in the homepage
     *
     * @return string
     */
    public function toTile()
    {
        $weather = new Weather($this->town, $this->country->getName());

        $meteoIcon   = $weather->getIcon();
        $temperature = $weather->getCurrentTemp();

        return HtmlTemplate::getTemplate('clockTile', array(
            'clockId'       => $this->id,
            'townName'      => $this->town,
            'countryName'   => $this->country->getName(),
            'timezoneName'  => $this->timezone->getName(),
            'timezoneOffset'=> $this->timezone->getOffset(),
            'meteoIcon'     => $meteoIcon,
            'temperature'   => $temperature
        ));
    }


    /**
     * Transforms the clock like a checkbox to know if the user wants to display it or not
     *
     * @param bool $isChecked
     *
     * @return string
     */
    public function toSearchItem($isChecked = false)
    {
        return HtmlTemplate::getTemplate('clockItem', array(
            'clockIdHtml'   => 'horloge_' . $this->id,
            'clockId'       => $this->id,
            'townName'      => $this->getTown(),
            'countryName'   => $this->country->getName(),
            'timezoneOffset'=> $this->timezone->getOffset(),
            'checked'       => $isChecked ? 'checked' : ''
        ));
    }
}