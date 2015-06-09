<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class CountryRepository implements InterfaceRepository{

    /**
     * @param int $id
     * @return Country
     */
    public static function findById($id)
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM pays WHERE id_pays = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createCountry($result);
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

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM pays WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $countries = array();

        foreach ($results as $result) {
            $countries[] = self::createCountry($result);
        }

        return $countries;
    }

    /**
     * @param array $result
     * @return Country
     */
    private static function createCountry($result)
    {
        $clocks = ClockRepository::findBy(array('pays_horloge' => $result['id_pays']));

        $country = new Country($result['id_pays'], $result['nom_pays']);
        $country->setClocks($clocks);

        return $country;
    }
}