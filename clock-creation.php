<?php
require_once 'pdo_connection.php';

if (isset($_POST['city']) && !empty($_POST['city']) && isset($_POST['country']) && !empty($_POST['country']) && isset($_POST['timezone']) && !empty($_POST['timezone'])) {

    $pdo        = pdo_connection::getPdo();
    $city       = htmlspecialchars($_POST['city']);
    $country    = (int) $_POST['country'];
    $timezone   = (int) $_POST['timezone'];

    $query = $pdo->prepare("INSERT INTO horloge (ville_horloge, pays_horloge, fuseau_horloge) VALUES (:city, :country, :timezone)");
    $query->execute(array(
        ':city'     => $city,
        ':country'  => $country,
        ':timezone' => $timezone,
    ));
    
    $clockId = $pdo->lastInsertId();

    /*
     * Gets the maximum order of the clock for that user
     */
    $query = $pdo->prepare("SELECT MAX(ordre_affichage) AS ordre FROM affichage WHERE utilisateur_affichage = :user");
    $query->execute(array(':user' => $_SESSION['id']));
    $maxOrder = $query->fetch()['ordre'] + 1;

    $query = $pdo->prepare("INSERT INTO affichage (utilisateur_affichage, horloge_affichage, ordre_affichage) VALUES (:user, :clock, :order)");
    $query->execute(array(
        ':user' => $_SESSION['id'],
        ':clock'=> $clockId,
        ':order'=> $maxOrder
    ));
}