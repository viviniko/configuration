<?php

namespace Viviniko\Configuration\Repositories\Variable;

interface VariableRepository
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Find data by key
     *
     * @param $key
     * @return mixed
     */
    public function findByKey($key);

    /**
     * Variable is exists.
     *
     * @param $column
     * @param null $value
     * @return mixed
     */
    public function exists($column, $value = null);

    /**
     * @param $key
     * @return mixed
     */
    public function deleteByKey($key);
}