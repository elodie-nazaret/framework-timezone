<?php

namespace timezone;

use timezone\connection\pdo_connection;
use timezone\entities\ClockRepository;
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

        foreach ($user->getViews() as $view) {
            if ($view->getOrder() >= $clockOrder && $view->getOrder() < $actualView->getOrder()) {
                $view->setOrder($view->getOrder() + 1);
                ViewRepository::update($view);
            }
        }

        $actualView->setOrder($clockOrder);
        ViewRepository::update($actualView);
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
}