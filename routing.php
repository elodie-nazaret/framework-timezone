<?php
    namespace timezone;

    require 'autoload.php';

    switch ($_GET['url']) {
        case 'accueil':
            if (isset($_POST['submit-gestion'])) {
                $controller = new Controller();
                $controller->gestion();
            }
            elseif (isset($_POST['submit-create-clock'])) {
                $controller = new Controller();
                $controller->createClock();
            }
            elseif (isset($_POST['disconnect'])) {
                Connection::getInstance()->disconnect();
            }
            $homepage = new Homepage();
            echo $homepage->toHtml();
            break;
        case 'weather_ajax':
            $controller = new Controller();
            echo $controller->getWeather();
            break;
        case 'update_order':
            $controller = new Controller();
            $controller->updateOrder();
            break;
    }