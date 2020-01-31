<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistsArrayRule implements Rule
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $message;

    /**
     * Create a new in rule instance.
     *
     * @param string $class
     * @param string $column
     */
    public function __construct(string $class, string $column = 'id')
    {
        $this->class = $class;
        $this->column = $column;
    }

    /**
     * @param string $message
     * @return \App\Rules\ExistsArrayRule
     */
    public function setMessage(string $message): ExistsArrayRule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  array $values
     * @return bool
     */
    public function passes($attribute, $values)
    {
        if (!is_array($values)) {
            return false;
        }

        /** @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = new $this->class;
        $query = $instance->whereIn($this->column, $values);

        return $query->count() === count($values);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message ?? trans('validation.exists_array_rule');
    }

    /**
     * @param string $class
     * @param string $column
     * @return \App\Rules\ExistsArrayRule
     */
    public static function make(string $class, string $column = 'id'): ExistsArrayRule
    {
        $instance = new self($class, $column);
        return $instance;
    }
}
