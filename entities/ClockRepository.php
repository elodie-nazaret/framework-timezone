<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class ClockRepository implements InterfaceRepository{

    /**
     * @param int $id
     * @return Clock
     */
    public static function findById($id)
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge WHERE id_horloge = :id");

        $query->execute(array(
            ':id' => $id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createClock($result);
    }

    /**
     * @param array $parameters
     * @return array
     */
    public static function findBy(array $parameters)
    {
        $where = array();
        $values = array();

        foreach ($parameters as $key => $value) {
            $where[] = $key . ' = :' . $key;
            $values[':' . $key] = $value;
        }

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM horloge WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $clocks = array();

        foreach ($results as $result) {
            $clocks[] = self::createClock($result);
        }

        return $clocks;
    }

    /**
     * @param array $result
     * @return Clock
     */
    private static function createClock($result) {
        $country    = CountryRepository::findById($result['pays_horloge']);
        $timezone   = TimezoneRepository::findById($result['fuseau_horloge']);
        $views      = ViewRepository::findBy(array('horloge_affichage' => $result['id_horloge']));

        $clock = new Clock($result['id_horloge'], $result['nom_pays'], $country, $timezone);
        $clock->setViews($views);

        return $clock;
    }
}