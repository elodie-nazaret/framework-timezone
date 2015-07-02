<?php

namespace Timezone\Entities;

use Framework\Entities\Entity;

/**
 * Class User
 */
class User extends Entity
{
    private $id;
    private $login;
    private $password;
    private $views = array();

    const TABLE_USER   = 'utilisateur';
    const COL_ID       = 'id_utilisateur';
    const COL_LOGIN    = 'login_utilisateur';
    const COL_PASSWORD = 'password_utilisateur';

    /**
     * @param int $id
     * @param string $login
     * @param string $password
     */
    public function __construct($id, $login, $password)
    {
        $this->id       = $id;
        $this->login    = $login;
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return View[]
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param View[] $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @param View $view
     */
    public function addView(View $view)
    {
        $this->views[] = $view;
    }

    /**
     * @param View $view
     */
    public function removeView(View $view) {
        $id = array_search($view, $this->views);
        if ($id !== false) {
            array_splice($this->views, $id, 1);
        }
    }
}
