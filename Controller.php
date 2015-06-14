<?php

namespace timezone;

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
}