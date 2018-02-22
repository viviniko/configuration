<?php

namespace Viviniko\Configuration\Repositories\Configable;

interface ConfigableRepository
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
     * Find data by field and value
     *
     * @param $column
     * @param null $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findBy($column, $value = null);

    /**
     * Variable is exists.
     *
     * @param $column
     * @param null $value
     * @return mixed
     */
    public function exists($column, $value = null);

    /**
     * @param $where
     * @return mixed
     */
    public function deleteBy($where);

    /**
     * @param $where
     * @return mixed
     */
    public function count($where);
}