<?php
session_start();
require_once 'pdo_connection.php';

if (!isset($_SESSION['id'])) {
    exit();
}

$clockId    = (int) $_POST['clockId'];
$clockOrder = (int) $_POST['clockOrder'];

$pdo = pdo_connection::getPdo();

$query = $pdo->prepare("SELECT ordre_affichage FROM affichage WHERE utilisateur_affichage = :user AND horloge_affichage = :clock");
$query->execute(array(
    ':user' => $_SESSION['id'],
    ':clock'=> $clockId,
));

/*
 * current order of the clock
 */
$order = $query->fetch()['ordre_affichage'];

$query = $pdo->prepare("UPDATE affichage SET ordre_affichage = ordre_affichage - 1 WHERE utilisateur_affichage = :user AND affichage.ordre_affichage > :order");
$query->execute(array(
    ':user' => $_SESSION['id'],
    ':order'=> $order
));

$query = $pdo->prepare("UPDATE affichage SET ordre_affichage = ordre_affichage + 1 WHERE utilisateur_affichage = :user AND affichage.ordre_affichage >= :order");
$query->execute(array(
    ':user' => $_SESSION['id'],
    ':order'=> $clockOrder
));

$query = $pdo->prepare("UPDATE affichage SET ordre_affichage = :order WHERE utilisateur_affichage = :user AND affichage.horloge_affichage = :clock");
$query->execute(array(
    ':order'=> $clockOrder,
    ':user' => $_SESSION['id'],
    ':clock'=> $clockId
));