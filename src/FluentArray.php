<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-27
 */

namespace fk\fluent;

/**
 * @method FluentArray only(array | string $keys, mixed $default = null)
 * @method FluentArray merge(array [] ...$arrays)
 * @method FluentArray mergeTo(array $array)  Merges to one the given array, value of which is taken as default value
 * @method FluentArray each(callable $callback)
 * @method FluentArray push(array ...$items)  To push items to the origin array
 */
class FluentArray extends FluentAbstract
{
    protected function validInput($data): bool
    {
        return is_array($data);
    }

    /**
     * @param mixed $default
     * @param array | string $keys
     * @return array
     */
    protected function fluentOnly($keys, $default = null)
    {
        $only = [];
        foreach ($keys as $key) {
            $only[$key] = $this->data[$key] ?? $default;
        }
        return $only;
    }

    /**
     * @param array[] ...$arrays
     * @return array
     */
    protected function fluentMerge(...$arrays)
    {
        return array_merge($this->data, ...$arrays);
    }

    /**
     * Merges to one the given array, value of which is taken as default value
     * @param array $array
     * @return array
     */
    protected function fluentMergeTo(array $array)
    {
        return array_merge($array, $this->data);
    }

    /**
     * @param callable $callback
     * @return array
     */
    protected function fluentEach(callable $callback)
    {
        return array_map($callback, $this->data);
    }

    /**
     * To push items to the origin array
     * @param array ...$items
     */
    protected function fluentPush(...$items)
    {
        array_push($this->data, ...$items);
    }

    public function keys()
    {
        return array_keys($this->data);
    }

    public function values()
    {
        return array_values($this->data);
    }

}