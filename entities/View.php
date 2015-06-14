<?php
namespace timezone\entities;

/**
 * Class View
 */
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
     * @param int $order
     */
    public function __construct($id, $order)
    {
        $this->id       = $id;
        $this->order    = $order;
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