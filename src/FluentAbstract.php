<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-27
 */

namespace fk\fluent;

use fk\exceptions\UndefinedMethodException;

/**
 * Abstract of fluent
 *
 * Each fluent method should start with prefix `fluent` such as `fluentGet`
 */
abstract class FluentAbstract
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * FluentAbstract constructor.
     * @param mixed $data
     */
    public function __construct($data)
    {
        if (!$this->validInput($data)) throw new \UnexpectedValueException('Trying to set value with wrong type of ' . gettype($data));
        $this->data = $data;
    }

    protected function validInput($data): bool
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' must be overwritten.');
    }

    public function base()
    {
        return $this->data;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $method = 'fluent' . ucfirst($name))) {
            $new = call_user_func_array([$this, $method], $arguments);
            if (is_array($new)) $this->data = $new;
            return $this;
        }
        throw new UndefinedMethodException($name);
    }

    /**
     * Static call of constructor
     * @param mixed $value
     * @return static
     */
    public static function build($value)
    {
        return new static($value);
    }
}