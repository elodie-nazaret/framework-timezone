<?php

namespace timezone\entities;

interface InterfaceRepository {

    /**
     * @param int $id
     * @return mixed
     */
    public static function findById($id);

    /**
     * @param array $parameters
     * @return array
     */
    public static function findBy(array $parameters);
}