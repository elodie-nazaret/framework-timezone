<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class TimezoneRepository implements InterfaceRepository{

    /**
     * @param int $id
     * @return Timezone
     */
    public static function findById($id)
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM fuseau WHERE id_fuseau = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createTimezone($result);
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

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM fuseau WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $timezones = array();

        foreach ($results as $result) {
            $timezones[] = self::createTimezone($result);
        }

        return $timezones;
    }

    /**
     * @param array $result
     * @return Timezone
     */
    private static function createTimezone($result)
    {
        $clocks = ClockRepository::findBy(array('fuseau_horloge' => $result['id_fuseau']));

        $timezone = new Timezone($result['id_fuseau'], $result['nom_fuseau'], $result['decalage_fuseau']);
        $timezone->setClocks($clocks);

        return $timezone;
    }
}