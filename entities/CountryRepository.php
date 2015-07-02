<?php

namespace Timezone\Entities;

use PDO;
use Timezone\Services\MySqlConnection;
use Framework\Entities\Repository;

/**
 * Class CountryRepository
 */
class CountryRepository implements Repository
{
    private static $countries = array();

    /**
     * @param int $id
     * @return Country
     */
    public static function findById($id)
    {
        if (isset(self::$countries[$id])) {
            return self::$countries[$id];
        }

        $query = MySqlConnection::getPdo()->prepare("SELECT * FROM " . Country::TABLE_COUNTRY . " WHERE " . Country::COL_ID . " = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createCountry($result);
    }

    /**
     * @param array $parameters
     * @return Country[]
     */
    public static function findBy(array $parameters)
    {
        $where = array();
        $values = array();

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $where[]            = $key . ' ' . $value['operator'] . ' :' . $key;
                $values[':' . $key] = $value['value'];
            } else {
                $where[]            = $key . ' = :' . $key;
                $values[':' . $key] = $value;
            }
        }

        $query = MySqlConnection::getPdo()->prepare("SELECT * FROM " . Country::TABLE_COUNTRY . " WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $countries = array();

        foreach ($results as $result) {
            $countries[] = self::createCountry($result);
        }

        return $countries;
    }

    /**
     * @return Country[]
     */
    public static function findAll()
    {
        $query = MySqlConnection::getPdo()->prepare("SELECT * FROM " . Country::TABLE_COUNTRY);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $countries = array();

        foreach ($results as $result) {
            $countries[] = self::createCountry($result);
        }

        return $countries;
    }

    /**
     * @param Country $country
     *
     * @return bool
     */
    public static function insert($country)
    {
        if ($country instanceof Country) {
            $query = MySqlConnection::getPdo()->prepare("INSERT INTO " . Country::TABLE_COUNTRY . "(" . Country::COL_NAME . ") VALUES (:nameCountry)");

            if ($query->execute(array(':nameCountry' => $country->getName()))) {
                $country->setId((int) MySqlConnection::getPdo()->lastInsertId());

                return true;
            }
        }

        return false;
    }

    /**
     * @param Country $country
     *
     * @return bool
     */
    public static function update($country)
    {
        if ($country instanceof Country) {
            $query = MySqlConnection::getPdo()->prepare("UPDATE " . Country::TABLE_COUNTRY . " SET " . Country::COL_NAME . " = :nameCountry WHERE " . Country::COL_ID . " = :id");

            return $query->execute(array(
                ':nameCountry' => $country->getName(),
                ':id'          => $country->getId()
            ));
        }
        return false;
    }

    /**
     * @param Country $country
     *
     * @return bool
     */
    public static function delete($country)
    {
        if ($country instanceof Country) {
            $query = MySqlConnection::getPdo()->prepare("DELETE FROM " . Country::TABLE_COUNTRY . " WHERE " . Country::COL_ID . " = :id");

            return $query->execute(array(
                ':id'       => $country->getId()
            ));
        }

        return false;
    }

    /**
     * @param array $result
     * @return Country
     */
    private static function createCountry($result)
    {
        if (isset(self::$countries[$result[Country::COL_ID]])) {
            return self::$countries[$result[Country::COL_ID]];
        }

        $country = new Country($result[Country::COL_ID], $result[Country::COL_NAME]);
        self::$countries[$country->getId()] = $country;

        $country->setClocks(ClockRepository::findBy(array(Clock::COL_COUNTRY => $result[Country::COL_ID])));

        return $country;
    }
}
