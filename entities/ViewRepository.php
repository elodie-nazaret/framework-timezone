<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class ViewRepository implements InterfaceRepository
{
    private static $views = array();

    /**
     * @param int $id
     * @return View
     */
    public static function findById($id)
    {
        if (isset(self::$views[$id])) {
            return self::$views[$id];
        }

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . View::TABLE_VIEW . " WHERE " . View::COL_ID . " = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createView($result);
    }

    /**
     * @param array $parameters
     * @return View[]
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

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . View::TABLE_VIEW . " WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $views = array();

        foreach ($results as $result) {
            $views[] = self::createView($result);
        }

        return $views;
    }

    /**
     * @return View[]
     */
    public static function findAll()
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . View::TABLE_VIEW);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $views = array();

        foreach ($results as $result) {
            $views[] = self::createView($result);
        }

        return $views;
    }

    /**
     * @param View $view
     *
     * @return bool
     */
    public static function insert($view)
    {
        if ($view instanceof View) {
            $query = pdo_connection::getPdo()->prepare("INSERT INTO " . View::TABLE_VIEW . "(" . View::COL_CLOCK . ", " . View::COL_ORDER . ", " . View::COL_USER .") VALUES (:clock, :orderView, :userId)");

            if ($query->execute(array(':clock' => $view->getClock()->getId(), ':orderView' => $view->getOrder(), ':userId' => $view->getUser()->getId()))) {
                $view->setId((int) pdo_connection::getPdo()->lastInsertId());

                return true;
            }
        }

        return false;
    }

    /**
     * @param View $view
     *
     * @return bool
     */
    public static function update($view)
    {
        if ($view instanceof View) {
            $query = pdo_connection::getPdo()->prepare("UPDATE " . View::TABLE_VIEW . " SET " . View::COL_CLOCK . " = :clock, " . View::COL_ORDER . " = :orderView, " . View::COL_USER . " = :userId WHERE " . View::COL_ID . " = :id");

            return $query->execute(array(
                ':clock'     => $view->getClock()->getId(),
                ':orderView' => $view->getOrder(),
                ':userId'    => $view->getUser()->getId(),
                ':id'        => $view->getId()
            ));
        }
        return false;
    }

    /**
     * @param View[]|View $views
     *
     * @return bool
     */
    public static function delete($views)
    {
        if (!is_array($views)) {
            $views = array($views);
        }

        $toDelete = array();
        foreach ($views as $view) {
            if ($view instanceof View) {
                $toDelete[] = $view->getId();
            }
        }

        if (!empty($toDelete)) {
            $query = pdo_connection::getPdo()->prepare("DELETE FROM " . View::TABLE_VIEW . " WHERE " . View::COL_ID . " IN (" .  implode(", ", $toDelete) . ")");
            return $query->execute();
        }

        return false;
    }

    /**
     * @param array $result
     * @return View
     */
    private static function createView($result)
    {
        if (isset(self::$views[$result[View::COL_ID]])) {
            return self::$views[$result[View::COL_ID]];
        }

        $view = new View($result[View::COL_ID], $result[View::COL_ORDER]);
        self::$views[$view->getId()] = $view;

        $view->setUser(UserRepository::findById($result[View::COL_USER]));
        $view->setClock(ClockRepository::findById($result[View::COL_CLOCK]));

        return $view;
    }
}