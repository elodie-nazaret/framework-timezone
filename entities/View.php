<?php
namespace timezone\entities;

class View
{
    private $id;
    private $user;
    private $clock;
    private $order;

    const TABLE_VIEW = 'affichage';
    const COL_ID     = 'id_affichage';
    const COL_CLOCK  = 'horloge_affichage';
    const COL_USER   = 'utilisateur_affichage';
    const COL_ORDER  = 'ordre_affichage';

    /**
     * @param int $id
     * @param User $user
     * @param Clock $clock
     * @param int $order
     *
     * @return View
     */
    public static function withId($id, User $user, Clock $clock, $order)
    {
        $view = new View();
        $view->setId($id);
        $view->setUser($user);
        $view->setClock($clock);
        $view->setOrder($order);

        return $view;
    }

    /**
     * @param User $user
     * @param Clock $clock
     * @param int $order
     *
     * @return View
     */
    public static function withoutId(User $user, Clock $clock, $order)
    {
        $view = new View();
        $view->setUser($user);
        $view->setClock($clock);
        $view->setOrder($order);

        return $view;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Clock
     */
    public function getClock()
    {
        return $this->clock;
    }

    /**
     * @param Clock $clock
     */
    public function setClock(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
}