<?php

namespace timezone;


use timezone\entities\Clock;
use timezone\entities\ClockRepository;
use timezone\entities\CountryRepository;
use timezone\entities\TimezoneRepository;
use timezone\entities\User;

class Homepage {
    const BASE_CLOCKS = "[1,2,3]";

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
        foreach (json_decode(Homepage::BASE_CLOCKS, true) as $clockId) {
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

        $html .= $this->generateHead();
        $html .= $this->generateBody();

        $html .='</html>';

        return $html;
    }

    /**
     * @return string
     */
    public function generateHead() {
        return <<<'HEAD'
<head lang="fr">
    <meta charset="UTF-8">
    <title>Timezone</title>
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/accueil.css">
</head>
HEAD;
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
        $header = Connection::getInstance()->isConnected() ? $this->generateConnectedHeader() : $this->generateNotConnectedHeader();
        return <<<SCRIPTS
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
            {$header}
        </div>
    </div>
</div>
SCRIPTS;
    }

    /**
     * @return string
     */
    private function generateNotConnectedHeader()
    {
        return <<<HEADER
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
                <form id="form-connection" method="post" action="{$_SERVER['PHP_SELF']}">
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
HEADER;
    }

    /**
     * @return string
     */
    private function generateConnectedHeader()
    {
        return <<<HEADER
<div class="pull-left">
    <div class="btn btn-default" id="button-switch-view"><span id="glyphicon-view" class="glyphicon glyphicon-list"></span>&nbsp;Passer en vue <span id="next-view-name">liste</span></div>
    <div class="btn btn-default" id="button-switch-clock"><span id="glyphicon-view" class="glyphicon glyphicon-list"></span>&nbsp;Passer en horloge <span id="next-clock-name">digitale</span></div>
    <div class="btn btn-info" id="button-manage"><span class="glyphicon glyphicon-wrench"></span>&nbsp;Gérer mes horloges</div>
</div>
<div class="pull-right">
    <form method="post" action="{$_SERVER['PHP_SELF']}">
        <button type="submit" class="btn btn-primary" id="button-disconnect" name="disconnect">Se déconnecter</button>
    </form>
</div>
HEADER;
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

        return <<<DIV
<div class="body container">
    <div id="clocks">
        {$tiles}
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
                            <form id="form-gestion" method="post" action="{$_SERVER['PHP_SELF']}">
                                <img src="images/ajax-loader.gif" alt="loader" class=" hidden center-block" id="ajax-loader"/>
                                <div id="results">
                                    {$clockSearchItems}
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
                    <form id="form-create-clock" method="post" action="{$_SERVER['PHP_SELF']}">
                        <img src="images/ajax-loader.gif" alt="loader" class="hidden center-block" id="ajax-loader"/>
                        <div class="form-group">
                            <label>Veuillez saisir une ville</label>
                            <input type="text" class="form-control" name="city" placeholder="Veuillez saisir une ville" required/>
                        </div>
                        <div class="form-group">
                            <label>Veuillez sélectionner un pays</label>
                            <select class="form-control" name="country">
                                {$countryOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Veuillez sélectionner un fuseau horaire</label>
                            <select class="form-control" name="timezone">
                                {$timezoneOptions}
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
DIV;
    }

    /**
     * @return string
     */
    private function generateFooter()
    {
        $script = '<script type="text/javascript" src="js/' . (Connection::getInstance()->isConnected() ? 'connection.js' : 'accueil.js') . '"></script>';
        return <<<FOOTER
<div>
    <script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/moment-timezone-data.js"></script>
    <script type="text/javascript" src="js/moment-timezone.js"></script>
    {$script}
</div>
FOOTER;
    }
}