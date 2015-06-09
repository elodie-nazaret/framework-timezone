<?php
namespace timezone\entities;

class View {
    private $id;
    private $user;
    private $clock;
    private $order;

    /**
     * @param int $id
     * @param User $user
     * @param Clock $clock
     * @param int $order
     */
    function __construct($id, User $user, Clock $clock, $order)
    {
        $this->id = $id;
        $this->user = $user;
        $this->clock = $clock;
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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