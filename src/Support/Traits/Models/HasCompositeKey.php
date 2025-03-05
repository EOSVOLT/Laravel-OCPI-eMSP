<?php

namespace Ocpi\Support\Traits\Models;

use Illuminate\Support\Arr;

trait HasCompositeKey
{
    protected $compositeKeyType = 'string';

    public $compositeIncrementing = false;

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return $this->compositeIncrementing;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return $this->compositeKeyType;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return Arr::map(
            Arr::wrap($this->getKeyName()),
            function (string $key) {
                return $this->getAttribute($key);
            });
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();

        return is_array($keys)
            ? $query->where(function ($query) use ($keys) {
                foreach ($keys as $key) {
                    $query->where($key, '=', $this->getAttribute($key));
                }
            })
            : parent::setKeysForSaveQuery($query);
    }
}
