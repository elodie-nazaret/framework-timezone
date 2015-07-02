<?php

namespace Timezone\Controllers;

use Framework\Controllers\Controller;
use Framework\HtmlTemplate;
use Timezone\Entities\Clock;
use Timezone\Entities\ClockRepository;
use Timezone\Entities\CountryRepository;
use Timezone\Entities\Timezone;
use Timezone\Entities\TimezoneRepository;
use Timezone\Entities\User;
use Timezone\Entities\View;
use Timezone\Entities\ViewRepository;
use Timezone\Services\Connection;

/**
 * Class HomepageController
 */
class HomepageController extends Controller
{
    const BASE_CLOCKS = '1,2,3';

    /* @var $clocks Clock[] */
    private $clocks;

    function __construct()
    {
        $this->clocks = array();

        if (!Connection::getInstance()->isConnected()) {
            $this->addBaseClocks();
        }
        else {
            $this->addUserClocks(Connection::getInstance()->getUser());
        }
    }

    /**
     * Even if the user is not connected, we display clocks that are defined by us
     */
    private function addBaseClocks()
    {
        foreach (explode(',', HomepageController::BASE_CLOCKS) as $clockId){
            $this->clocks[] = ClockRepository::findById($clockId);
        }
    }

    /**
     * @param User $user
     */
    private function addUserClocks(User $user)
    {
        $views = $user->getViews();

        if (empty($views)) {
            $order = 0;
            foreach (explode(',', HomepageController::BASE_CLOCKS) as $clockId) {
                $view = new View(0, ++$order);
                $view->setClock(ClockRepository::findById($clockId));
                $view->setUser($user);
                ViewRepository::insert($view);

                $views[] = $view;
            }
        }

        foreach ($views as $view) {
            $this->clocks[] = $view->getClock();
        }
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return HtmlTemplate::getTemplate('homepage', array(
            'header'            => $this->getHeader(),
            'target'            => $_SERVER['REDIRECT_URL'],
            'tiles'             => $this->getTilesHtml(),
            'clockSearchItems'  => $this->getClockSearchItemsHtml(),
            'countryOptions'    => $this->getCountryOptionsHtml(),
            'timezoneOptions'   => $this->getTimezoneOptionsHtml(),
            'script'            => Connection::getInstance()->isConnected() ? '' : '<script type="text/javascript" src="public/js/connection.js"></script>'
        ));
    }

    private function getHeader()
    {
        return HtmlTemplate::getTemplate((Connection::getInstance()->isConnected() ? 'connectedHeader' : 'notConnectedHeader'), array(
            'target'    => $_SERVER['REDIRECT_URL']
        ));
    }

    /**
     * @return string
     */
    private function getTilesHtml()
    {
        $tiles = '';
        foreach ($this->clocks as $clock) {
            $tiles .= $clock->toTile();
        }

        return $tiles;
    }

    /**
     * @return string
     */
    private function getClockSearchItemsHtml()
    {
        $clockSearchItems = '';
        $clocks           = ClockRepository::findAll();
        foreach ($clocks as $clock) {
            $clockSearchItems .= $clock->toSearchItem(in_array($clock, $this->clocks));
        }

        return $clockSearchItems;
    }

    /**
     * @return string
     */
    private function getCountryOptionsHtml()
    {
        $countryOptions = '';
        $countries      = CountryRepository::findAll();
        foreach($countries as $country) {
            $countryOptions .= $country->toOption();
        }

        return $countryOptions;
    }

    /**
     * @return string
     */
    private function getTimezoneOptionsHtml()
    {
        $timezoneOptions = '';
        $timezones       = TimezoneRepository::query(
            "SELECT * FROM (SELECT * FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . Timezone::COL_OFFSET . " LIKE '%-%' ORDER BY " . Timezone::COL_OFFSET . " DESC) neg UNION SELECT * FROM (SELECT * FROM ". Timezone::TABLE_TIMEZONE . " WHERE " . Timezone::COL_OFFSET . " LIKE '%+%' ORDER BY " . Timezone::COL_OFFSET . ") pos"
        );
        foreach($timezones as $timezone) {
            $timezoneOptions .= $timezone->toOption();
        }

        return $timezoneOptions;
    }
}
