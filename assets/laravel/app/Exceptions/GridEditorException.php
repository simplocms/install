<?php

namespace App\Exceptions;

use App\Structures\GridEditor\ContentItem;

class GridEditorException extends \Exception
{
    const INVALID_ITEM_TYPE = 1;

    const INVALID_TAG = 6;
    const INVALID_MODULE_CONFIGURATION = 7;
    const INVALID_MODULE = 8;
    const INVALID_ENTITY_ID = 9;
    const INVALID_ATTRIBUTES = 10;

    /** @var \App\Structures\GridEditor\ContentItem */
    public $item;

    /**
     * Invalid item attributes.
     *
     * @param string $path
     * @return \App\Exceptions\GridEditorException
     */
    public static function invalidItemType(string $path): GridEditorException
    {
        return new self("Invalid item type. Path {$path}.", self::INVALID_ITEM_TYPE);
    }

    /**
     * Invalid module.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return \App\Exceptions\GridEditorException
     */
    public static function invalidModule(ContentItem $item): GridEditorException
    {
        return self::forItem($item, 'Invalid module.', self::INVALID_MODULE);
    }


    /**
     * Invalid ID of module entity.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return \App\Exceptions\GridEditorException
     */
    public static function invalidModuleEntityId(ContentItem $item): GridEditorException
    {
        return self::forItem($item, 'Invalid module entity ID.', self::INVALID_ENTITY_ID);
    }


    /**
     * Invalid configuration of module entity.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return \App\Exceptions\GridEditorException
     */
    public static function invalidModuleConfiguration(ContentItem $item): GridEditorException
    {
        return self::forItem(
            $item, 'Invalid module configuration.', self::INVALID_MODULE_CONFIGURATION
        );
    }


    /**
     * Invalid item attributes.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return \App\Exceptions\GridEditorException
     */
    public static function invalidItemAttributes(ContentItem $item): GridEditorException
    {
        return self::forItem($item, 'Invalid attributes of content item.', self::INVALID_ATTRIBUTES);
    }


    /**
     * Invalid item tag.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @return \App\Exceptions\GridEditorException
     */
    public static function invalidItemTag(ContentItem $item): GridEditorException
    {
        return self::forItem($item, 'Invalid tag of content item.', self::INVALID_TAG);
    }


    /**
     * Make exception for specific item.
     *
     * @param \App\Structures\GridEditor\ContentItem $item
     * @param string $message
     * @param int $code
     * @return \App\Exceptions\GridEditorException
     */
    public static function forItem(ContentItem $item, string $message, int $code): GridEditorException
    {
        $exception = new self(join(' ', [
            $message,
            "For item {$item->getType()} with path {$item->getPath()}."
        ]), $code);
        $exception->item = $item;

        return $exception;
    }
}