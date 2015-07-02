<?php

namespace Timezone\Config;

use Timezone\Controllers\AjaxController;
use Timezone\Controllers\HomepageController;
use Timezone\Services\Connection;

/**
 * Class Rooter
 */
class Rooter
{
    public function __construct()
    {
        switch ($_GET['url']) {
            case 'accueil':
                if (isset($_POST['submit-gestion'])) {
                    $controller = new AjaxController();
                    $controller->gestion();
                }
                elseif (isset($_POST['submit-create-clock'])) {
                    $controller = new AjaxController();
                    $controller->createClock();
                }
                elseif (isset($_POST['disconnect'])) {
                    Connection::getInstance()->disconnect();
                }
                $homepage = new HomepageController();
                echo $homepage->toHtml();
                break;
            case 'weather_ajax':
                $controller = new AjaxController();
                echo $controller->getWeather();
                break;
            case 'update_order':
                $controller = new AjaxController();
                $controller->updateOrder();
                break;
        }
    }
}
