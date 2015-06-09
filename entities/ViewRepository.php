<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class ViewRepository implements InterfaceRepository{

    /**
     * @param int $id
     * @return View
     */
    public static function findById($id)
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM affichage WHERE id_affichage = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createView($result);
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

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM utilisateur WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $views = array();

        foreach ($results as $result) {
            $views[] = self::createView($result);
        }

        return $views;
    }

    /**
     * @param array $result
     * @return View
     */
    private static function createView($result) {
        $user   = UserRepository::findById($result['utilisateur_affichage']);
        $clock  = ClockRepository::findById($result['horloge_affichage']);

        $view = new View($result['id_affichage'], $user, $clock, $result['ordre_affichage']);

        return $view;
    }
}