<?php

namespace App\Structures\Enums;

use App\Helpers\UrlFactory;
use App\Helpers\WidgetFactory;
use App\Models\Module\Repository;
use App\Models\Web\Theme;
use App\Services\MediaLibrary\MediaLibrary;
use App\Services\ResponseManager\ResponseManager;
use App\Services\Settings\Settings;
use App\Services\UniversalModules\Collector;
use App\Structures\Collections\LanguagesCollection;

final class SingletonEnum extends AbstractEnum
{
    /** @var \App\Models\Web\Theme */
    const THEME = 'theme';

    /** @var \App\Services\Settings\Settings */
    const SETTINGS = 'settings';

    /** @var \App\Structures\Collections\LanguagesCollection */
    const LANGUAGES_COLLECTION = 'languages_collection';

    /** @var \App\Helpers\UrlFactory */
    const URL_FACTORY = 'url_factory';

    /** @var \App\Services\MediaLibrary\MediaLibrary */
    const MEDIA_LIBRARY = 'media_library';

    /** @var \App\Helpers\WidgetFactory */
    const WIDGET_FACTORY = 'widget_factory';

    /** @var \App\Services\UniversalModules\Collector */
    const UNIVERSAL_MODULES = 'universal_module_collector';

    /** @var \App\Models\Module\Repository */
    const MODULES = 'modules';

    /** @var \App\Services\ResponseManager\ResponseManager */
    const RESPONSE_MANAGER = 'response_manager';

    /**
     * Theme singleton instance.
     *
     * @return \App\Models\Web\Theme
     */
    public static function theme(): Theme
    {
        return resolve(self::THEME);
    }


    /**
     * Settings singleton instance.
     *
     * @return \App\Services\Settings\Settings
     */
    public static function settings(): Settings
    {
        return resolve(self::SETTINGS);
    }


    /**
     * Language collection singleton instance.
     *
     * @return \App\Structures\Collections\LanguagesCollection
     */
    public static function languagesCollection(): LanguagesCollection
    {
        return resolve(self::LANGUAGES_COLLECTION);
    }


    /**
     * Url factory singleton instance.
     *
     * @return \App\Helpers\UrlFactory
     */
    public static function urlFactory(): UrlFactory
    {
        return resolve(self::URL_FACTORY);
    }


    /**
     * Media library singleton instance.
     *
     * @return \App\Services\MediaLibrary\MediaLibrary
     */
    public static function mediaLibrary(): MediaLibrary
    {
        return resolve(self::MEDIA_LIBRARY);
    }


    /**
     * Widget factory singleton instance.
     *
     * @return \App\Helpers\WidgetFactory
     */
    public static function widgetFactory(): WidgetFactory
    {
        return resolve(self::WIDGET_FACTORY);
    }


    /**
     * Universal modules collector singleton instance.
     *
     * @return \App\Services\UniversalModules\Collector
     */
    public static function universalModules(): Collector
    {
        return resolve(self::UNIVERSAL_MODULES);
    }


    /**
     * Modules repository singleton instance.
     *
     * @return \App\Models\Module\Repository
     */
    public static function modules(): Repository
    {
        return resolve(self::MODULES);
    }


    /**
     * Modules repository singleton instance.
     *
     * @return \App\Services\ResponseManager\ResponseManager
     */
    public static function responseManager(): ResponseManager
    {
        return resolve(self::RESPONSE_MANAGER);
    }
}
