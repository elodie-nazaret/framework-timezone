<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class UserRepository implements InterfaceRepository{

    /**
     * @param int $id
     * @return User
     */
    public static function findById($id)
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createUser($result);
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
        $users = array();

        foreach ($results as $result) {
            $users[] = self::createUser($result);
        }

        return $users;
    }

    /**
     * @param array $result
     * @return User
     */
    private static function createUser($result) {
        $views = ViewRepository::findBy(array('utilisateur_affichage' => $result['id_utilisateur']));

        $user = new User($result['id_utilisateur'], $result['login_utilisateur'], $result['password_utilisateur']);
        $user->setViews($views);

        return $user;
    }
}