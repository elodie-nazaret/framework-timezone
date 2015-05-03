<?php

$pdo = pdo_connection::getPdo();
$query = $pdo->prepare("SELECT * FROM horloge INNER JOIN affichage ON (affichage.horloge_affichage = horloge.id_horloge) WHERE affichage.utilisateur_affichage = :id");
$query->execute(array(':id' => $_SESSION['id']));
$clocks = $query->fetchAll(PDO::FETCH_ASSOC);

$wanted = array_keys($_POST);
unset($wanted[array_search('submit-gestion', $wanted)]);

$maxOrder  = 0;
$clocksIds = array();

foreach ($clocks as $clock) {
    $clocksIds[] = $clock['id_horloge'];

    if (!in_array($clock['id_horloge'], $wanted)) {
        $query = $pdo->prepare("DELETE FROM affichage WHERE horloge_affichage = :horloge AND utilisateur_affichage = :utilisateur");
        $query->execute(array(':horloge' => $clock['id_horloge'], ':utilisateur' => $_SESSION['id']));

        $query = $pdo->prepare("UPDATE affichage SET ordre_affichage = ordre_affichage - 1 WHERE utilisateur_affichage = :utilisateur and ordre_affichage > :ordre");
        $query->execute(array(':utilisateur' => $_SESSION['id'], ':ordre' => $clock['ordre_affichage']));

        $maxOrder--;
    }
    $maxOrder++;
}

foreach ($wanted as $wantedClock) {
    if (!in_array($wantedClock, $clocksIds)) {
        $query = $pdo->prepare("INSERT INTO affichage VALUES (:utilisateur, :horloge, :ordre )");
        $query->execute(array(':utilisateur' => $_SESSION['id'], ':horloge' => $wantedClock, ':ordre' => ++$maxOrder));
    }
}

