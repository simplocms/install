<?php
/**
 * CKEditor.php created by Patrik Václavek
 */

namespace App\Structures\FormFields;


class CKEditor extends TextArea
{
    /**
     * CKEditor constructor.
     * @param string $name
     * @param string|null $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct($name, $label);
        $this->type = self::TYPE_CKEDITOR;
    }
}
