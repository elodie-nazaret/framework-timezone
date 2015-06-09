<?php
namespace timezone\entities;

class User {
    private $id;
    private $login;
    private $password;
    private $views;

    /**
     * @param int $id
     * @param string $login
     * @param string $password
     */
    function __construct($id, $login, $password)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->views = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param array $views
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