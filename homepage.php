<?php
namespace timezone;

use timezone\entities\Clock;
use timezone\entities\ClockRepository;
use timezone\entities\CountryRepository;
use timezone\entities\TimezoneRepository;
use timezone\entities\User;

class Homepage {
    const BASE_CLOCKS = [1,2,3];

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

    private function addBaseClocks() {
        foreach (Homepage::BASE_CLOCKS as $clockId) {
            $this->clocks[] = ClockRepository::findById($clockId);
        }
    }

    /**
     * @param User $user
     */
    private function addUserClocks(User $user) {
        $views = $user->getViews();

        foreach ($views as $view) {
            $this->clocks[] = $view->getClock();
        }
    }

    /**
     * @return string
     */
    public function toHtml() {
        $html = '<!DOCTYPE html>
        <html>';

        $html .= HtmlTemplate::getTemplate('head', array());
        $html .= $this->generateBody();

        $html .='</html>';

        return $html;
    }

    /**
     * @return string
     */
    public function generateBody() {
        $return = '<body>';

        $return .= $this->generateHeader();
        $return .= $this->generateContent();
        $return .= $this->generateFooter();

        $return .= '</body>';

        return $return;
    }

    /**
     * @return string
     */
    public function generateHeader() {
        $header = HtmlTemplate::getTemplate((Connection::getInstance()->isConnected() ? 'connectedHeader' : 'notConnectedHeader'), array(
            'target'    => $_SERVER['REDIRECT_URL']
        ));

        return HtmlTemplate::getTemplate('header', array(
            'header'    => $header
        ));
    }

    /**
     * @return string
     */
    private function generateContent()
    {
        $tiles = '';
        foreach ($this->clocks as $clock) {
            $tiles .= $clock->toTile();
        }

        $clockSearchItems = '';
        $clocks = ClockRepository::findAll();
        foreach ($clocks as $clock) {
            $clockSearchItems .= $clock->toSearchItem(in_array($clock, $this->clocks));
        }

        $countryOptions = '';
        $countries = CountryRepository::findAll();
        foreach($countries as $country) {
            $countryOptions .= $country->toOption();
        }

        $timezoneOptions = '';
        $timezones = TimezoneRepository::findAll();
        foreach($timezones as $timezone) {
            $timezoneOptions .= $timezone->toOption();
        }
        
        return HtmlTemplate::getTemplate('content', array(
            'target'            => $_SERVER['REDIRECT_URL'],
            'tiles'             => $tiles,
            'clockSearchItems'  => $clockSearchItems,
            'countryOptions'    => $countryOptions,
            'timezoneOptions'   => $timezoneOptions
        ));
    }

    /**
     * @return string
     */
    private function generateFooter()
    {
        return HtmlTemplate::getTemplate('footer', array(
            'script' => Connection::getInstance()->isConnected() ? '' : '<script type="text/javascript" src="js/connection.js"></script>'
        ));
    }
}