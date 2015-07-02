<?php

namespace Timezone\Entities;

use PDO;
use Timezone\Services\MySqlConnection;
use Framework\Entities\Repository;

/**
 * Class TimezoneRepository
 */
class TimezoneRepository implements Repository
{
    private static $timezones = array();

    /**
     * @param int $id
     * @return Timezone
     */
    public static function findById($id)
    {
        if (isset(self::$timezones[$id])) {
            return self::$timezones[$id];
        }

        $query = MySqlConnection::getPdo()->prepare("SELECT * FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . Timezone::COL_ID . " = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createTimezone($result);
    }

    /**
     * @param array $parameters
     * @return Timezone[]
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

        $query = MySqlConnection::getPdo()->prepare("SELECT * FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $timezones = array();

        foreach ($results as $result) {
            $timezones[] = self::createTimezone($result);
        }

        return $timezones;
    }

    /**
     * @return timezone[]
     */
    public static function findAll()
    {
        $query = MySqlConnection::getPdo()->prepare("SELECT * FROM " . Timezone::TABLE_TIMEZONE);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $timezones = array();

        foreach ($results as $result) {
            $timezones[] = self::createTimezone($result);
        }

        return $timezones;
    }

    /**
     * @param $queryString
     *
     * @return array
     */
    public static function query($queryString)
    {
        $query = MySqlConnection::getPdo()->prepare($queryString);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $timezones = array();

        foreach ($results as $result) {
            $timezones[] = self::createTimezone($result);
        }

        return $timezones;
    }

    /**
     * @param Timezone $timezone
     *
     * @return bool
     */
    public static function insert($timezone)
    {
        if ($timezone instanceof Timezone) {
            $query = MySqlConnection::getPdo()->prepare("INSERT INTO " . Timezone::TABLE_TIMEZONE . "(" . Timezone::COL_NAME . ", " . Timezone::COL_OFFSET .") VALUES (:nameTimezone, :offset)");

            if ($query->execute(array(':nameTimezone' => $timezone->getName(), ':offset' => $timezone->getOffset()))) {
                $timezone->setId((int) MySqlConnection::getPdo()->lastInsertId());

                return true;
            }
        }

        return false;
    }

    /**
     * @param Timezone $timezone
     *
     * @return bool
     */
    public static function update($timezone)
    {
        if ($timezone instanceof Timezone) {
            $query = MySqlConnection::getPdo()->prepare("UPDATE " . Timezone::TABLE_TIMEZONE . " SET " . Timezone::COL_NAME . " = :nameTimezone, " . Timezone::COL_OFFSET . " = :offset WHERE " . Timezone::COL_ID . " = :id");

            return $query->execute(array(
                ':nameTimezone' => $timezone->getName(),
                ':offset'       => $timezone->getOffset(),
                ':id'           => $timezone->getId()
            ));
        }
        return false;
    }

    /**
     * @param Timezone $timezone
     *
     * @return bool
     */
    public static function delete($timezone)
    {
        if ($timezone instanceof Timezone) {
            $query = MySqlConnection::getPdo()->prepare("DELETE FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . Timezone::COL_ID . " = :id");

            return $query->execute(array(
                ':id' => $timezone->getId()
            ));
        }

        return false;
    }

    /**
     * @param array $result
     * @return Timezone
     */
    private static function createTimezone($result)
    {
        if (isset(self::$timezones[$result[Timezone::COL_ID]])) {
            return self::$timezones[$result[Timezone::COL_ID]];
        }

        $timezone = new Timezone($result[Timezone::COL_ID], $result[Timezone::COL_NAME], $result[Timezone::COL_OFFSET]);
        self::$timezones[$timezone->getId()] = $timezone;

        $timezone->setClocks(ClockRepository::findBy(array(Clock::COL_TIMEZONE => $result[Timezone::COL_ID])));

        return $timezone;
    }
}
