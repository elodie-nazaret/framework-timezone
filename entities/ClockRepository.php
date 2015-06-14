<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class ClockRepository implements InterfaceRepository
{
    private static $clocks = array();
    /**
     * @param int $id
     * @return Clock
     */
    public static function findById($id)
    {
        if (isset(self::$clocks[$id])) {
            return self::$clocks[$id];
        }

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . Clock::TABLE_CLOCK . " WHERE " . Clock::COL_ID . " = :id");

        $query->execute(array(
            ':id' => $id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createClock($result);
    }

    /**
     * @param array $parameters
     * @return Clock[]
     */
    public static function findBy(array $parameters)
    {
        $where = array();
        $values = array();

        foreach ($parameters as $key => $value) {
            $where[] = $key . ' = :' . $key;
            $values[':' . $key] = $value;
        }

//        debug_print_backtrace();
//        echo '<br /><br /><br />';
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . Clock::TABLE_CLOCK . " WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $clocks = array();

        foreach ($results as $result) {
            $clocks[] = self::createClock($result);
        }

        return $clocks;
    }

    /**
     * @return Clock[]
     */
    public static function findAll()
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . Clock::TABLE_CLOCK);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $clocks = array();

        foreach ($results as $result) {
            $clocks[] = self::createClock($result);
        }

        return $clocks;
    }

    /**
     * @param Clock $clock
     *
     * @return bool
     */
    public static function insert($clock)
    {
        if ($clock instanceof Clock) {
            $query = pdo_connection::getPdo()->prepare("INSERT INTO " . Clock::TABLE_CLOCK . "(" . Clock::COL_TOWN . ", " . Clock::COL_COUNTRY . ", " . Clock::COL_TIMEZONE . ") VALUES (:town, :country, :timezone)");

            return $query->execute(array(
                ':town'     => $clock->getTown(),
                ':country'  => $clock->getCountry()->getId(),
                ':timezone' => $clock->getTimezone()->getId()
            ));
        }

        return false;
    }

    /**
     * @param Clock $clock
     *
     * @return bool
     */
    public static function update($clock)
    {
        if ($clock instanceof Clock) {
            $query = pdo_connection::getPdo()->prepare("UPDATE " . Clock::TABLE_CLOCK . " SET " . Clock::COL_TOWN . " = :town, " . Clock::COL_COUNTRY . " = :country, " . Clock::COL_TIMEZONE . " = :timezone WHERE " . Clock::COL_ID . " = :id");

            return $query->execute(array(
                ':town'     => $clock->getTown(),
                ':country'  => $clock->getCountry()->getId(),
                ':timezone' => $clock->getTimezone()->getId(),
                ':id'       => $clock->getId()
            ));
        }
        return false;
    }

    /**
     * @param Clock $clock
     *
     * @return bool
     */
    public static function delete($clock)
    {
        if ($clock instanceof Clock) {
            $query = pdo_connection::getPdo()->prepare("DELETE FROM " . Clock::TABLE_CLOCK . " WHERE " . Clock::COL_ID . " = :id");

            return $query->execute(array(
                ':id'       => $clock->getId()
            ));
        }

        return false;
    }

    /**
     * @param array $result
     * @return Clock
     */
    private static function createClock($result) {
        if (isset(self::$clocks[$result[Clock::COL_ID]])) {
            return self::$clocks[$result[Clock::COL_ID]];
        }

        $clock = new Clock($result[Clock::COL_ID], $result[Clock::COL_TOWN]);
        self::$clocks[$clock->getId()] = $clock;

        $clock->setCountry(CountryRepository::findById($result[Clock::COL_COUNTRY]));
        $clock->setTimezone(TimezoneRepository::findById($result[Clock::COL_TIMEZONE]));
        $clock->setViews(ViewRepository::findBy(array(View::COL_CLOCK => $result[Clock::COL_ID])));

        return $clock;
    }
}