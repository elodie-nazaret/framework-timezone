<?php
namespace timezone\entities;

class Clock {
    private $id;
    private $town;
    private $country;
    private $timezone;
    private $views;

    /**
     * @param int $id
     * @param string $town
     * @param Country $country
     * @param Timezone $timezone
     */
    function __construct($id, $town, Country $country, Timezone $timezone)
    {
        $this->id = $id;
        $this->town = $town;
        $this->country = $country;
        $this->timezone = $timezone;
        $this->views = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param array $views
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
    public function removeView(View $view) {
        $id = array_search($view, $this->views);
        if ($id !== false) {
            array_splice($this->views, $id, 1);
        }
    }
    
}