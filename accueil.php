<?php
namespace timezone;

use PDO;
use timezone\connection\pdo_connection;

session_start();

    if (!empty($_POST)) {
        if (isset($_POST['disconnect'])) {
            session_destroy();
            session_start();
        } elseif (isset($_POST['submit-gestion'])){
            require 'gestion.php';
        } elseif (isset($_POST['submit-create-clock'])) {
            require 'clock-creation.php';
        } else {
            require 'connection_old.php';
        }
    }
?>
<!DOCTYPE html>
<html>
<head lang="fr">
    <meta charset="UTF-8">
    <title>Timezone</title>
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/accueil.css">
</head>
<body>
    <script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/moment-timezone-data.js"></script>
    <script type="text/javascript" src="js/moment-timezone.js"></script>

    <div class="header text-center container">
        <div class="row">
            <div class="col-md-12">
                <h1>Timezone</h1>
                <?php
                if (!isset($_SESSION['id'])) {
                    ?>
                    <div class="pull-right">
                        <div class="btn btn-success" id="button-signup">S'inscrire</div>
                        <div class="btn btn-primary" id="button-signin"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Se connecter</div>
                    </div>

                    <div class="modal fade" id="modal-connection">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close reset-form-connection" data-dismiss="modal" aria-label="Fermer">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title">Connexion</h4>
                                </div>
                                <div class="modal-body">
                                    <form id="form-connection" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <div class="form-group">
                                            <label for="input-username">Nom d'utilisateur</label>
                                            <input type="text" class="form-control" id="input-username" name="username" pattern="^[a-zA-Z0-9._-]+$" title="Caractères alphanumériques, tiret, underscore ou point autorisés"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="input-password">Mot de passe</label>
                                            <input type="password" class="form-control" id="input-password" name="password"/>
                                        </div>
                                        <input type="hidden" name="type"/>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default reset-form-connection" data-dismiss="modal">
                                        <span class="glyphicon glyphicon-remove"></span>&nbsp;Annuler
                                    </button>
                                    <button type="submit" form="form-connection" class="btn btn-success">
                                        <span class="glyphicon glyphicon-ok"></span>&nbsp;Valider
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                else {
                    ?>
                    <div class="pull-left">
                        <div class="btn btn-default" id="button-switch-view"><span id="glyphicon-view" class="glyphicon glyphicon-list"></span>&nbsp;Passer en vue <span id="next-view-name">liste</span></div>
                        <div class="btn btn-default" id="button-switch-clock"><span id="glyphicon-view" class="glyphicon glyphicon-list"></span>&nbsp;Passer en horloge <span id="next-clock-name">digitale</span></div>
                        <div class="btn btn-info" id="button-manage"><span class="glyphicon glyphicon-wrench"></span>&nbsp;Gérer mes horloges</div>
                    </div>
                    <div class="pull-right">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <button type="submit" class="btn btn-primary" id="button-disconnect" name="disconnect">Se déconnecter</button>
                        </form>
                    </div>

                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="body container">
        <div id="clocks">
            <?php
                $clocks = array();
                $checkedClocks = array();

                if (isset($_SESSION['id'])) {
                    $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge INNER JOIN affichage ON (affichage.horloge_affichage = horloge.id_horloge) INNER JOIN fuseau ON (fuseau.id_fuseau = horloge.fuseau_horloge) INNER JOIN pays ON (pays.id_pays = horloge.pays_horloge) WHERE affichage.utilisateur_affichage = :id ORDER BY affichage.ordre_affichage");
                    $query->execute(array(
                        ':id' => $_SESSION['id']
                    ));
                    $clocks = $query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($clocks as $clock) {
                        $checkedClocks[] = $clock['id_horloge'];
                    }
                }

                if (empty($clocks)) {
                    $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge INNER JOIN fuseau ON (fuseau.id_fuseau = horloge.fuseau_horloge) INNER JOIN pays ON (pays.id_pays = horloge.pays_horloge) WHERE horloge.id_horloge < 4");
                    $query->execute();
                    $clocks = $query->fetchAll(PDO::FETCH_ASSOC);
                }

                foreach ($clocks as $clock) {
                    $weather = file_get_contents('http://api.openweathermap.org/data/2.5/weather?q=' . $clock['ville_horloge'] . '&APPID=87ebbac3eaa1d68a0e59a741fc5ef5c3');
                    $weather = json_decode($weather, true);

                    ?>
                    <div class="clock col-xs-6 col-sm-4 clock-grid">
                        <div class="clock-id hidden"><?php echo $clock['id_horloge'] ?></div>
                        <div class="clock-city"><?php echo $clock['ville_horloge'] ?></div>
                        <div class="clock-country"><?php echo $clock['nom_pays'] ?></div>
                        <div class="clock-date"></div>
                        <div class="clock-timezone hidden"><?php echo $clock['nom_fuseau'] ?></div>
                        <div class="clock-timezone-offset"><?php echo $clock['decalage_fuseau'] ?></div>
                        <div class="clock-clock">
                            <svg class="clock-analog" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160" preserveAspectRatio="xMidYMid meet">
                                <g>
                                    <circle r="78" cy="80" cx="80" stroke-width="4" stroke="#FFFFFF" fill="none"></circle>
                                    <g>
                                        <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(30 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(60 80, 80)"></rect>
                                        <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(90 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(120 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(150 80, 80)"></rect>
                                        <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(180 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(210 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(240 80, 80)"></rect>
                                        <rect height="15" width="4" y="10" x="78" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(270 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(300 80, 80)"></rect>
                                        <rect height="10" width="2" y="10" x="79" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" transform="rotate(330 80, 80)"></rect>
                                    </g>
                                    <g>
                                        <rect height="75" width="1" y="70" x="79.5" rx="1" ry="1" stroke="#FFFFFF" fill="#FFFFFF" class="second-hand"></rect>
                                        <rect height="60" width="2" y="70" x="79" rx="2" ry="2" stroke="#FFFFFF" fill="#FFFFFF" class="minute-hand"></rect>
                                        <rect height="45" width="3" y="70" x="78.5" rx="3" ry="3" stroke="#FFFFFF" fill="#FFFFFF" class="hour-hand"></rect>
                                    </g>
                                </g>
                            </svg>
                            <div class="clock-digital"><span class="clock-digital-hour">10</span>:<span class="clock-digital-minute">15</span>:<span class="clock-digital-second">20</span></div>
                            <div class="clock-ampm"></div>
                            <div class="clock-weather"><img src="http://openweathermap.org/img/w/<?php echo $weather['weather'][0]['icon']; ?>.png" alt="Météo" title="Météo"/></div>
                            <div class="clock-temp"><?php echo round($weather['main']['temp'] - 273.15, 1) ?> °C</div>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div class="detail">
            <div class="row">
                <div class="detail-head col-xs-12 text-center">
                    <div class="detail-city"></div>
                    <div class="detail-country"></div>
                    <div class="detail-date"></div>
                    <div class="detail-timezone-offset"></div>
                </div>
            </div>
            <div class="row">
                <div class="detail-body col-xs-12">
                    <div class="detail-body-left pull-left col-xs-7">
                        <div class="detail-clock"></div>
                    </div>
                    <div class="detail-body-right col-xs-5 text-right">
                        <div class="detail-weather"></div>
                        <div class="detail-temp-current"></div>
                        <div class="detail-humidity"></div>
                        <div class="detail-pressure"></div>
                        <div class="detail-temp-min"></div>
                        <div class="detail-temp-max"></div>
                        <div class="detail-wind"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-gestion">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close reset-form-gestion" data-dismiss="modal" aria-label="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Gérer mes horloges</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-10 col-xs-offset-1">
                                <form class="searchAjax" action="search.php" method="post">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="search" id="search" placeholder="Rechercher un pays ou une ville"/>
                                    </div>
                                    <br/>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="form-gestion" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <img src="images/ajax-loader.gif" alt="loader" class=" hidden center-block" id="ajax-loader"/>
                                    <div id="results">
                                        <?php
                                        $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge INNER JOIN fuseau ON (fuseau.id_fuseau = horloge.fuseau_horloge) INNER JOIN pays ON (pays.id_pays = horloge.pays_horloge) ORDER BY pays.nom_pays");
                                        $query->execute();
                                        $clocks = $query->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($clocks as $clock) {
                                            echo '<div class="col-xs-10">';
                                            echo '<label for="horloge_' . $clock['id_horloge'] . '"><h4>' . $clock['nom_pays'] . ', ' . $clock['ville_horloge'] . ', ' . $clock['decalage_fuseau'] . '</h4></label>';
                                            echo '</div>';
                                            echo '<div class="col-xs-2">';
                                            echo '<input type="checkbox" style="transform: scale(1.2); -webkit-transform: scale(1.2);" id="horloge_' . $clock['id_horloge'] . '" name="' . $clock['id_horloge'] . '"';
                                            if (in_array($clock['id_horloge'], $checkedClocks)) {
                                                echo 'checked';
                                            }
                                            echo '>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-info reset-form-gestion" id="button-create-clock" data-dismiss="modal">
                            <span class="glyphicon glyphicon-wrench"></span>&nbsp;Créer une horloge
                        </button>
                        <button type="button" class="btn btn-default reset-form-gestion" data-dismiss="modal">
                            <span class="glyphicon glyphicon-remove"></span>&nbsp;Annuler
                        </button>
                        <button type="submit" class="btn btn-success" form="form-gestion" name="submit-gestion">
                            <span class="glyphicon glyphicon-ok"></span>&nbsp;Valider
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-create-clock">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close reset-form-create-clock" data-dismiss="modal" aria-label="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Créer une horloge</h3>
                    </div>
                    <div class="modal-body">
                        <form id="form-create-clock" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <img src="images/ajax-loader.gif" alt="loader" class="hidden center-block" id="ajax-loader"/>
                            <div class="form-group">
                                <label>Veuillez saisir une ville</label>
                                <input type="text" class="form-control" name="city" placeholder="Veuillez saisir une ville" required/>
                            </div>
                            <div class="form-group">
                                <label>Veuillez sélectionner un pays</label>
                                <select class="form-control" name="country">
                                    <?php
                                    $countryResult  = pdo_connection::getPdo()->query("SELECT * FROM pays ORDER BY nom_pays");
                                    while($country = $countryResult->fetch()) {
                                        echo '<option value="' . $country['id_pays'] . '">' . $country['nom_pays'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Veuillez sélectionner un fuseau horaire</label>
                                <select class="form-control" name="timezone">
                                    <?php
                                    $timezoneResult = pdo_connection::getPdo()->query("SELECT * FROM (SELECT * FROM fuseau WHERE decalage_fuseau LIKE '%-%' ORDER BY decalage_fuseau DESC) neg UNION SELECT * FROM (SELECT * FROM fuseau WHERE decalage_fuseau LIKE '%+%' ORDER BY decalage_fuseau) pos");
                                    while($timezone = $timezoneResult->fetch()) {
                                        echo '<option value="' . $timezone['id_fuseau'] . '">' . $timezone['nom_fuseau'] . ' ' . $timezone['decalage_fuseau'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default reset-form-create-clock" data-dismiss="modal">
                            <span class="glyphicon glyphicon-remove"></span>&nbsp;Annuler
                        </button>
                        <button type="submit" class="btn btn-success" form="form-create-clock" name="submit-create-clock">
                            <span class="glyphicon glyphicon-ok"></span>&nbsp;Valider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/moment.js"></script>
        <script type="text/javascript" src="js/moment-timezone-data.js"></script>
        <script type="text/javascript" src="js/moment-timezone.js"></script>

        <?php
            if (!isset($_SESSION['id'])) {
                echo '<script type="text/javascript" src="js/connection.js"></script>';
            }

            echo '<script type="text/javascript" src="js/accueil.js"></script>';
        ?>
    </div>
</body>
</html>
