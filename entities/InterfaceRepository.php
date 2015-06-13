<?php

namespace timezone\entities;

use stdClass;

interface InterfaceRepository {

    /**
     * @param int $id
     *
     * @return mixed
     */
    public static function findById($id);

    /**
     * @param array $parameters
     *
     * @return array
     */
    public static function findBy(array $parameters);

    /**
     * @return array
     */
    public static function findAll();

    /**
     * @param $object
     *
     * @return bool
     */
    public static function insert($object);

    /**
     * @param $object
     *
     * @return bool
     */
    public static function update($object);

    /**
     * @param $object
     *
     * @return bool
     */
    public static function delete($object);
}