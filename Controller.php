<?php

namespace timezone;

use timezone\connection\pdo_connection;
use timezone\entities\Clock;
use timezone\entities\ClockRepository;
use timezone\entities\CountryRepository;
use timezone\entities\Timezone;
use timezone\entities\TimezoneRepository;
use timezone\entities\View;
use timezone\entities\ViewRepository;

/**
 * Class Controller
 */
class Controller
{
    /**
     * updateOrder
     */
    public function updateOrder()
    {
        if (Connection::getInstance()->isConnected()) {
            $clockId    = (int) $_POST['clockId'];
            $clockOrder = (int) $_POST['clockOrder'];
            $user       = Connection::getInstance()->getUser();
            $actualView = null;

            foreach ($user->getViews() as $view) {
                if ($view->getClock()->getId() == $clockId) {
                    $actualView = $view;
                    break;
                }
            }

            $minOrder   = min($clockOrder, $actualView->getOrder());
            $maxOrder   = max($clockOrder, $actualView->getOrder());
            $way        = ($clockOrder < $actualView->getOrder() ? '+' : '-');

            foreach ($user->getViews() as $view) {
                if (
                    ($way == '+' && $view->getOrder() >= $minOrder && $view->getOrder() < $maxOrder)
                    || ($way == '-' && $view->getOrder() > $minOrder && $view->getOrder() <= $maxOrder)
                ) {
                    $view->setOrder((int)eval('return (' . $view->getOrder() . $way . '1);'));
                    ViewRepository::update($view);
                }
            }

            $actualView->setOrder($clockOrder);
            ViewRepository::update($actualView);
        }
    }

    public function gestion()
    {
        $user = Connection::getInstance()->getUser();

        $wanted = array_keys($_POST);
        unset($wanted[array_search('submit-gestion', $wanted)]);

        ViewRepository::delete($user->getViews());
        $user->setViews(array());

        $order = 1;

        foreach ($wanted as $wantedClock) {
            $view = new View(0, $order);
            $view->setUser($user);
            $view->setClock(ClockRepository::findById($wantedClock));
            if (ViewRepository::insert($view)) {
                $user->addView($view);
            }
            $order++;
        }
    }

    public function getWeather()
    {
        $city = $_POST['city'];
        $country = $_POST['country'];
        $weather = new Weather($city, $country);

        return json_encode(array(
            'humidity' => $weather->getHumidity(),
            'pressure' => $weather->getPressure(),
            'minTemp' => $weather->getMinTemp(),
            'maxTemp' => $weather->getMaxTemp(),
            'wind' => $weather->getWind(),
            'icon' => $weather->getIcon(),
            'temp' => $weather->getCurrentTemp()
        ));
    }

    public function createClock()
    {
        if (isset($_POST['city']) && !empty($_POST['city']) && isset($_POST['country']) && !empty($_POST['country']) && isset($_POST['timezone']) && !empty($_POST['timezone'])) {
            $city       = htmlspecialchars($_POST['city']);
            $country    = (int) $_POST['country'];
            $timezone   = (int) $_POST['timezone'];

            $clock = new Clock(0, $city);
            $clock->setCountry(CountryRepository::findById($country));
            $clock->setTimezone(TimezoneRepository::findById($timezone));
            ClockRepository::insert($clock);

            $user   = Connection::getInstance()->getUser();
            $order  = count($user->getViews()) + 1;

            $view = new View(0, $order);
            $view->setUser($user);
            $view->setClock($clock);
            ViewRepository::insert($view);

            $user->addView($view);
        }
    }
}