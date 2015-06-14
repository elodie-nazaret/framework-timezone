<?php
    namespace timezone;

    require 'autoload.php';

    switch ($_GET['url']) {
        case 'accueil':
            $homepage = new Homepage();
            echo $homepage->toHtml();
            break;
    }