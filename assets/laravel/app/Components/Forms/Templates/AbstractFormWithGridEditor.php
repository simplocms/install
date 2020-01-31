<?php

namespace App\Components\Forms\Templates;

use App\Models\Module\InstalledModule;
use App\Components\Forms\AbstractForm;
use App\Structures\Enums\SingletonEnum;

abstract class AbstractFormWithGridEditor extends AbstractForm
{
    /**
     * Use grid editor versions?
     *
     * @var boolean
     */
    protected $useVersions = false;

    /**
     * Set data variables for grid editor.
     *
     * @param array $mergeWith
     * 
     * @return array
     */
    public function getGridEditorData(array $mergeWith = []): array
    {
        $content = old('content');
        if (is_null($content)) {
            $content = optional($this->getGridEditorContent())->getRaw();
        }

        return array_merge([
            '_GE_CONTENT' => $content,
            '_GE_USE_VERSIONS' => $this->useVersions,
            '_GE_VERSIONS' => $this->useVersions ? $this->getGridEditorVersions() : null,
            '_GE_MODULES' => $this->getModulesJson(),
            '_GE_UNIVERSAL_MODULES' => $this->getUniversalModulesJson(),
            '_GE_CAN_EDIT_LAYOUT' => auth()->user()->can('ge-edit-layout'),
            '_GE_URL_PREVIEWS' => route('admin.grideditor.modules.previews'),
            '_GE_URL_EDIT' => route('admin.grideditor.modules.edit'),
            '_GE_URL_VALIDATION' => route('admin.grideditor.modules.validateAndPreview'),
            '_GE_URL_ENTITY' => route('admin.grideditor.modules.entity'),
            '_GE_URL_VERSION_SWITCH' => $this->getGridEditorVersionSwitchUrl(),
            '_GE_URL_UNIVERSAL_VALIDATION' => route('admin.universalmodule.validateAndPreview'),
            '_GE_URL_UNIVERSAL_PREVIEWS' => route('admin.universalmodule.previews'),
            '_GE_URL_UNIVERSAL_EDIT' => route('admin.universalmodule.editForm'),
        ], $mergeWith);
    }


    /**
     * Get modules for grid editor in json format.
     *
     * @return string
     */
    protected function getModulesJson(): string
    {
        return json_encode(
            array_values(
                InstalledModule::enabled()
                    ->get()
                    ->pluck('module')
                    ->filter(
                        function ($module) {
                            return $module && $module->isForGridEditor();
                        }
                    )
                    ->map(
                        function ($module) {
                            return $module->toArray();
                        }
                    )
                    ->toArray()
            )
        );
    }


    /**
     * Get universal modules for grid editor in json format.
     *
     * @return string
     */
    protected function getUniversalModulesJson(): string
    {
        $universalModules = [];

        foreach (SingletonEnum::universalModules()->all() as $key => $module) {
            $universalModules[] = [
                'icon' => $module->getIcon(),
                'name' => $key,
                'title' => $module->getName(),
                'url' => route('admin.universalmodule.newForm', $key),
                'universal' => true
            ];
        }

        return json_encode($universalModules);
    }


    /**
     * Get content of the grid editor.
     *
     * @return \App\Models\Interfaces\IsGridEditorContent
     */
    abstract protected function getGridEditorContent(): \App\Models\Interfaces\IsGridEditorContent;


    /**
     * Get url of the version switch for the grid editor.
     *
     * @return string
     */
    protected function getGridEditorVersionSwitchUrl(): string
    {
        return "";
    }


    /**
     * Get versions of the grid editor content.
     *
     * @return array
     */
    protected function getGridEditorVersions(): array
    {
        return [];
    }


    /**
     * Register scripts and style of grid editor to the form.
     *
     * @return void
     * @throws \Exception
     */
    protected function addGridEditorScriptsAndStyle()
    {
        $this->addScript(url('plugin/js/beautify.js'));
        $this->addScript(url('plugins/ace/ace.js'));
        $this->addScript(mix('js/color-picker.js'));
        $this->addScript(mix('js/grideditor.js'));
    }
}
