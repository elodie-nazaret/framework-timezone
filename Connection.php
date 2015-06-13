<?php
namespace timezone;

use timezone\entities\User;
use timezone\entities\UserRepository;

session_start();


class Connection {
    private $connected;
    private $user;

    private static $instance;

    /**
     * @return Connection
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Connection();
        }

        return self::$instance;
    }

    private function __construct()
    {
        if (isset($_SESSION['id'])) {
            $this->connected = true;
        }
        else {
            $this->connected = false;
            $this->processPost();
        }
    }

    public function processPost()
    {
        if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['type']) && !empty($_POST['type'])) {

            $username   = htmlspecialchars($_POST['username']);
            $password   = hash('sha256', htmlspecialchars($_POST['password']));
            $type       = htmlspecialchars($_POST['type']);

            if (preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {

                if ($type == 'signup') {
                    $this->signup($username, $password);
                }

                if ($type == 'signin' || $type == 'signup') {
                    $this->signin($username, $password);
                }
            }
        }
    }

    /**
     * @param string $login
     * @param string $password
     */
    public function signup($login, $password)
    {
        $user = new User(0, $login, $password);

        if (UserRepository::insert($user)) {
            $this->connect($user);
        }
    }

    /**
     * @param string $login
     * @param string $password
     */
    public function signin($login, $password)
    {
        $user = UserRepository::findBy(array(
            "login_utilisateur" => $login,
            "password_utilisateur" => $password
        ));

        if (sizeof($user) == 1) {
            $this->connect($user[0]);
        }
    }

    /**
     * @param User $user
     */
    private function connect(User $user) {
        $this->isConnected(true);
        $this->user = $user;
        $_SESSION['id']= $user->getId();
    }

    /**
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


}