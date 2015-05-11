<?php

session_start();
require_once 'pdo_connection.php';

$query = pdo_connection::getPdo()->prepare("SELECT id_horloge FROM horloge INNER JOIN affichage ON (affichage.horloge_affichage = horloge.id_horloge) WHERE affichage.utilisateur_affichage = :id");
$query->execute(array(':id' => $_SESSION['id']));
$checkedClocks = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($checkedClocks as $checkedClock) {
    $checkedClocksIds[] = $checkedClock['id_horloge'];
}

if ($_POST['search'] != null) {
    $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge INNER JOIN fuseau ON (fuseau.id_fuseau = horloge.fuseau_horloge) INNER JOIN pays ON (pays.id_pays = horloge.pays_horloge) WHERE pays.nom_pays LIKE :search OR horloge.ville_horloge LIKE :search ORDER BY pays.nom_pays");
    $query->execute(array(':search' => htmlspecialchars('%' . $_POST['search'] . '%')));
} else {
    $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge INNER JOIN fuseau ON (fuseau.id_fuseau = horloge.fuseau_horloge) INNER JOIN pays ON (pays.id_pays = horloge.pays_horloge) ORDER BY pays.nom_pays");
    $query->execute();
}
$clocks = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($clocks)) {
    echo '<div class="alert alert-warning" role="alert">Aucun résultat</div>';
} else {
    foreach ($clocks as $clock) {
        echo '<div class="col-xs-10">';
        echo '<label for="horloge_' . $clock['id_horloge'] . '"><h4>' . $clock['nom_pays'] . ', ' . $clock['ville_horloge'] . ', ' . $clock['decalage_fuseau'] . '</h4></label>';
        echo '</div>';
        echo '<div class="col-xs-2">';
        echo '<input type="checkbox" style="transform: scale(1.2); -webkit-transform: scale(1.2);" id="horloge_' . $clock['id_horloge'] . '" name="' . $clock['id_horloge'] . '"';
        if (in_array($clock['id_horloge'], $checkedClocksIds)) {
            echo 'checked';
        }
        echo '>';
        echo '</div>';
    }
}

