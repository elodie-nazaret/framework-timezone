<?php
    session_start();
    require_once 'pdo_connection.php';

    if (!empty($_POST)) {
        if (isset($_POST['disconnect'])) {
            session_destroy();
            session_start();
        }
        else {
            require 'connection.php';
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
</head>
<body>
    <div class="header">
        <h1>Timezone</h1>
        <?php
            if (!isset($_SESSION['username'])) {
                ?>
                <div class="btn btn-primary" id="button-signup">S'inscrire</div>
                <div class="btn btn-primary" id="button-signin">Se connecter</div>

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
                                    Annuler
                                </button>
                                <button type="submit" form="form-connection" class="btn btn-primary">Valider</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            else {
                ?>
                <div class="btn btn-primary" id="button-manage">Gérer</div>
                <div class="btn btn-primary" id="button-switch-view">Passer en vue <span id="next-view-name">liste</span></div>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <button type="submit" class="btn btn-primary" id="button-disconnect" name="disconnect">Se déconnecter</button>
                </form>
                <?php
            }
        ?>
    </div>
    <div class="body">
        <?php
        //            getHorloges($_SESSION['username']);
        ?>
    </div>
    <div class="footer">
        <script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>

        <?php
            if (!isset($_SESSION['username'])) {
                echo '<script type="text/javascript" src="js/connection.js"></script>';
            } else {
//                echo '<script type="text/javascript" src="js/accueil.js"></script>';
            }
        ?>
    </div>
</body>
</html>