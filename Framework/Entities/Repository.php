<?php

namespace Framework\Entities;

/**
 * Interface Repository
 */
interface Repository
{

    /**
     * @param int $id
     *
     * @return Entity
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
     * @param Entity $object
     *
     * @return bool
     */
    public static function insert($object);

    /**
     * @param Entity $object
     *
     * @return bool
     */
    public static function update($object);

    /**
     * @param Entity $object
     *
     * @return bool
     */
    public static function delete($object);
}
