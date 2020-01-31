<?php
/**
 * TextArea.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;


class TextArea extends TextInput
{
    /**
     * Select options.
     *
     * @var array
     */
    protected $rows;

    /**
     * TextArea constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct($name, $label);
        $this->type = self::TYPE_TEXTAREA;
    }


    /**
     * Set select options.
     *
     * @param int $rows
     * @return \App\Structures\FormFields\TextArea
     */
    public function rows(int $rows): TextArea
    {
        $this->rows = $rows;
        return $this;
    }


    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'rows' => $this->rows,
        ]);
    }
}
