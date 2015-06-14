<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class TimezoneRepository implements InterfaceRepository
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

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . Timezone::COL_ID . " = :id");

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
            $where[] = $key . ' = :' . $key;
            $values[':' . $key] = $value;
        }

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . implode(' AND ', $where));
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
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . Timezone::TABLE_TIMEZONE);
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
            $query = pdo_connection::getPdo()->prepare("INSERT INTO " . Timezone::TABLE_TIMEZONE . "(" . Timezone::COL_NAME . ", " . Timezone::COL_OFFSET .") VALUES (:nameTimezone, :offset)");

            return $query->execute(array(
                ':nameTimezone' => $timezone->getName(),
                ':offset'       => $timezone->getOffset()
            ));
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
            $query = pdo_connection::getPdo()->prepare("UPDATE " . Timezone::TABLE_TIMEZONE . " SET " . Timezone::COL_NAME . " = :nameTimezone, " . Timezone::COL_OFFSET . " = :offset WHERE " . Timezone::COL_ID . " = :id");

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
            $query = pdo_connection::getPdo()->prepare("DELETE FROM " . Timezone::TABLE_TIMEZONE . " WHERE " . Timezone::COL_ID . " = :id");

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