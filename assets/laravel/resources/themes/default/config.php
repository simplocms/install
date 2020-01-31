<?php

return [
    'name' => 'Default',

    'menu_locations' => [
        'primary' => 'theme::theme.menu_locations.primary',
        'footer' => 'theme::theme.menu_locations.footer'
    ],

    'settings' => [
        '%lang%_articles_page_id' => \App\Services\Settings\Settings::TYPE_INT
    ],

    'universal_modules' => [
        \App\Services\UniversalModules\UniversalModule::make(
            'speakers', 'theme::universal_modules.speakers.name', 'bullhorn'
        )
            ->allowOrdering()
            ->withUrl()
            ->setFields([
                \App\Structures\FormFields\TextInput::make(
                    'name', 'theme::universal_modules.speakers.labels.name'
                )->required(),
                \App\Structures\FormFields\CKEditor::make(
                    'company', 'theme::universal_modules.speakers.labels.company'
                )->required(),
                \App\Structures\FormFields\TextArea::make(
                    'info', 'theme::universal_modules.speakers.labels.info'
                )->required(),
                \App\Structures\FormFields\Image::make(
                    'image', 'theme::universal_modules.speakers.labels.image'
                )->required(),
                \App\Structures\FormFields\Image::make(
                    'image_header', 'theme::universal_modules.speakers.labels.image_header'
                )->required(),
            ]),

        \App\Services\UniversalModules\UniversalModule::make(
            'companies', 'theme::universal_modules.companies.name', 'building-o'
        )
            ->setFields([
                \App\Structures\FormFields\TextInput::make(
                    'name', 'theme::universal_modules.companies.labels.name'
                )->required(),
                \App\Structures\FormFields\Image::make(
                    'image', 'theme::universal_modules.companies.labels.image'
                )->required(),
                \App\Structures\FormFields\MediaFile::make(
                    'pdf', 'theme::universal_modules.companies.labels.pdf'
                )->acceptedType('application/pdf'),
            ]),
    ]
];
