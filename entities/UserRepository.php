<?php
namespace timezone\entities;

use PDO;
use timezone\connection\pdo_connection;

class UserRepository implements InterfaceRepository
{

    private static $users = array();

    /**
     * @param int $id
     * @return User
     */
    public static function findById($id)
    {
        if (isset(self::$users[$id])) {
            return self::$users[$id];
        }

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . User::TABLE_USER . " WHERE " . User::COL_ID . " = :id");

        $query->execute(array(
            ':id' =>$id
        ));

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return self::createUser($result);
    }

    /**
     * @param array $parameters
     * @return User[]
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

        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . User::TABLE_USER . " WHERE " . implode(' AND ', $where));
        $query->execute($values);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $users = array();

        foreach ($results as $result) {
            $users[] = self::createUser($result);
        }

        return $users;
    }

    /**
     * @return User[]
     */
    public static function findAll()
    {
        $query = pdo_connection::getPdo()->prepare("SELECT * FROM " . User::TABLE_USER);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $users = array();

        foreach ($results as $result) {
            $users[] = self::createUser($result);
        }

        return $users;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public static function insert($user)
    {
        if ($user instanceof User) {
            $query = pdo_connection::getPdo()->prepare("INSERT INTO " . User::TABLE_USER . "(" . User::COL_LOGIN . ", " . User::COL_PASSWORD .") VALUES (:login, :password)");

            return $query->execute(array(
                ':login'    => $user->getLogin(),
                ':password' => $user->getPassword()
            ));
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public static function update($user)
    {
        if ($user instanceof User) {
            $query = pdo_connection::getPdo()->prepare("UPDATE " . User::TABLE_USER . " SET " . User::COL_LOGIN . " = :login, " . User::COL_PASSWORD . " = :password WHERE " . User::COL_ID . " = :id");

            return $query->execute(array(
                ':login'    => $user->getLogin(),
                ':password' => $user->getPassword(),
                ':id'       => $user->getId()
            ));
        }
        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public static function delete($user)
    {
        if ($user instanceof User) {
            $query = pdo_connection::getPdo()->prepare("DELETE FROM " . User::TABLE_USER . " WHERE " . User::COL_ID . " = :id");

            return $query->execute(array(
                ':id' => $user->getId()
            ));
        }

        return false;
    }

    /**
     * @param array $result
     * @return User
     */
    private static function createUser($result) {

        if (isset(self::$users[$result[User::COL_ID]])) {
            return self::$users[$result[User::COL_ID]];

        }

        $user = new User($result[User::COL_ID], $result[User::COL_LOGIN], $result[User::COL_PASSWORD]);
        self::$users[$user->getId()] = $user;

        $user->setViews(ViewRepository::findBy(array(View::COL_USER => $result[User::COL_ID])));

        return $user;
    }
}