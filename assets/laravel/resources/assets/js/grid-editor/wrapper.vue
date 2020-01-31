<template>
    <div class="grideditor" :class="{'_grid-content-only': !layoutEditMode}">
        <!-- Control panel -->
        <div class="control-panel clearfix">
            <!-- Layouts -->
            <div class="pull-left _grid-layout-size-controls">
                <button type="button" class="btn btn-grideditor btn-with-info"
                        :class="{active: activeLayout === 'xl'}"
                        @click.prevent="changeLayout('xl')">
                    <i class="fa fa-desktop"></i> {{ localization.trans('sizes.xl') }} (xl)
                    <small>≥ 1200px</small>
                </button>
                <button type="button" class="btn btn-grideditor btn-with-info"
                        :class="{active: activeLayout === 'lg'}"
                        @click.prevent="changeLayout('lg')">
                    <i class="fa fa-laptop"></i> {{ localization.trans('sizes.lg') }} (lg)
                    <small>≥ 992px</small>
                </button>
                <button type="button" class="btn btn-grideditor btn-with-info"
                        :class="{active: activeLayout === 'md'}"
                        @click.prevent="changeLayout('md')">
                    <i class="fa fa-tablet fa-rotate-270"></i> {{ localization.trans('sizes.md') }} (md)
                    <small>≥ 768px</small>
                </button>
                <button type="button" class="btn btn-grideditor btn-with-info"
                        :class="{active: activeLayout === 'sm'}"
                        @click.prevent="changeLayout('sm')">
                    <i class="fa fa-tablet"></i> {{ localization.trans('sizes.sm') }} (sm)
                    <small>≥ 576px</small>
                </button>
                <button type="button" class="btn btn-grideditor btn-with-info"
                        :class="{active: activeLayout === null}"
                        @click.prevent="changeLayout()">
                    <i class="fa fa-mobile-phone"></i> {{ localization.trans('sizes.xs') }}
                    <small>< 576px</small>
                </button>
            </div>

            <div class="pull-right">
                <!-- Mode switch -->
                <button type="button" class="btn btn-grideditor"
                        @click.prevent="switchMode"
                        :disabled="!canEditLayout"
                >
                    <i class="fa fa-pencil"></i>
                    <span v-if="contentEditMode">{{ localization.trans('modes.layout') }}</span>
                    <span v-else>{{ localization.trans('modes.content') }}</span>
                </button>

                <!-- Version switch -->
                <div class="pull-right dropdown version-switch" v-if="useVersions && activeVersion">
                    <button type="button" class="btn btn-grideditor version dropdown-toggle"
                            data-toggle="dropdown"
                            aria-expanded="false"
                    >
                        <i class="fa fa-code-fork"></i>
                        {{ localization.trans('version_text', activeVersion) }}
                        <small>{{ activeVersion.date }}</small>
                        <small>{{ activeVersion.author }}</small>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li v-for="(version, index) in versions">
                            <a href="#" :class="{active: version.index === activeVersion.index}"
                               @click.prevent="confirmVersionChange(index)"
                            >
                                {{ localization.trans('version_text', version) }}
                                <small>{{ version.date }}</small>
                                <small>{{ version.author }}</small>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <content-root :item="rootItem"
                      ref="content"
                      :layout-edit-mode="layoutEditMode"
                      :localization="localization"
        ></content-root>

        <!-- Container Settings Modal-->
        <container-settings-modal :allowed-tags="allowedTags" :localization="localization"></container-settings-modal>

        <!-- Row Settings Modal-->
        <row-settings-modal :allowed-tags="allowedTags" :localization="localization"></row-settings-modal>

        <!-- Column Settings Modal-->
        <column-settings-modal :allowed-tags="allowedTags" :localization="localization"></column-settings-modal>

        <!-- Modules Modal-->
        <modules-modal :modules="modules"
                       :universal-modules="universalModules"
                       :localization="localization"
        ></modules-modal>

        <!-- Row Layouts Modal -->
        <row-layouts-modal :localization="localization"></row-layouts-modal>

        <!-- Module Form Modal-->
        <module-form-modal :form-uri="urls.editModule"
                           :form-universal-uri="urls.editUniversalModule"
                           :validate-uri="urls.validateModule"
                           :validate-universal-uri="urls.validateUniversalModule"
                           :localization="localization"
        ></module-form-modal>
    </div>
</template>

<script>
    // Content
    import ContentRoot from './content/content-root';
    import ContentContainer from './content/content-container';
    import ContentRow from './content/content-row';
    import ContentColumn from './content/content-column';
    import ContentModule from './content/content-module';

    // Modals
    import ContainerSettingsModal from './modals/container-settings-modal';
    import RowSettingsModal from './modals/row-settings-modal';
    import ColumnSettingsModal from './modals/column-settings-modal';
    import ModulesModal from './modals/modules-modal';
    import ModuleFormModal from './modals/module-form-modal';
    import RowLayoutsModal from './modals/row-layouts-modal';

    // Enums
    import {EVENTS} from './enums';

    // Mixins
    import LocalizationMixin from '../vue-mixins/localization';

    // Content components
    Vue.component('cms-ge-content-container', ContentContainer);
    Vue.component('cms-ge-content-row', ContentRow);
    Vue.component('cms-ge-content-column', ContentColumn);
    Vue.component('cms-ge-content-module', ContentModule);

    const MODES = {
        CONTENT: 1,
        LAYOUT: 2
    };

    const options = window.gridEditorOptions();

    export default {
        mixins: [LocalizationMixin],

        data() {
            return {
                activeLayout: 'lg',
                mode: options.canEditLayout ? MODES.LAYOUT : MODES.CONTENT,
                versions: options.versions || [],
                useVersions: options.useVersions || false,
                activeVersionIndex: 0,
                canEditLayout: options.canEditLayout || false,
                content: [...options.content] || [],
                modules: options.modules || [],
                universalModules: options.universalModules || [],
                allowedTags: options.allowedTags || [],
                urls: options.urls,
            };
        },

        components: {
            'content-root': ContentRoot,
            'container-settings-modal': ContainerSettingsModal,
            'row-settings-modal': RowSettingsModal,
            'column-settings-modal': ColumnSettingsModal,
            'modules-modal': ModulesModal,
            'module-form-modal': ModuleFormModal,
            'row-layouts-modal': RowLayoutsModal,
        },

        created() {
            this.initialize();
            this.$root.$on(EVENTS.LAYOUT_CHANGED, this.changeLayout);
            this.$root.$on(EVENTS.CONTENT_CHANGED, this.changeContent);
        },

        mounted() {
            this.$root.$emit(EVENTS.LAYOUT_CHANGED, this.activeLayout);
        },

        destroyed() {
            this.$root.$off(EVENTS.LAYOUT_CHANGED, this.changeLayout);
            this.$root.$off(EVENTS.CONTENT_CHANGED, this.changeContent);
        },

        methods: {
            getFormData() {
                const data = {content: this.getContent()};

                if (this.useVersions && this.activeVersion) {
                    data.active_content_id = this.activeVersion.id
                }
                return data;
            },

            getContent() {
                return this.content;
            },

            changeLayout(layout = null) {
                this.activeLayout = layout;
                window.GE.activeLayout = layout;
            },

            changeContent() {
                this.content = this.rootItem.content;
            },

            switchMode() {
                if (this.mode === MODES.CONTENT && this.canEditLayout) {
                    this.mode = MODES.LAYOUT;
                } else if (this.mode === MODES.LAYOUT) {
                    this.mode = MODES.CONTENT;
                }
            },

            changeVersion(index) {
                const versionId = this.versions[index].id;
                const $content = $(this.$refs.content.$el);
                $content.lock();

                const versionUrl = this.urls.switchVersion + '/' + versionId;

                Request.get(versionUrl)
                    .done(response => {
                        this.activeVersionIndex = index;
                        this.setContent(response.content);
                    })
                    .always(() => {
                        $content.unlock();
                    });
            },

            initialize() {
                if (this.useVersions) {
                    this.activeVersionIndex = this.versions.findIndex(version => version.isActive);
                }

                this.fetchModulePreviews();

                window.GE = {
                    activeLayout: this.activeLayout
                };
            },

            fetchModulePreviews() {
                const entityIds = this.getEntitiesInContent(this.content);

                if (entityIds.entities.length) {
                    Request.get(this.urls.modulePreviews, {ids: JSON.stringify(entityIds.entities)})
                        .done(response => {
                            if (!response.modules) {
                                return;
                            }

                            response.modules.forEach(module => {
                                this.$root.$emit(EVENTS.PREVIEW_LOADED + module.id, module);
                            });
                        });
                }

                if (entityIds.universalEntities.length) {
                    Request.get(this.urls.universalModulePreviews, {ids: JSON.stringify(entityIds.universalEntities)})
                        .done(response => {
                            if (!response.modules) {
                                return;
                            }

                            response.modules.forEach(module => {
                                this.$root.$emit(EVENTS.UNIVERSAL_PREVIEW_LOADED + module.id, module);
                            });
                        });
                }
            },

            getEntitiesInContent(content) {
                const entityIds = [];
                const universalEntityIds = [];

                for (const i in content) {
                    content[i].uuid = Utils.guid();

                    if (content[i].type === 'module' && content[i].entity_id) {
                        if (content[i].universal) {
                            universalEntityIds.push(content[i].entity_id);
                        } else {
                            entityIds.push(content[i].entity_id);
                        }
                    }

                    if (content[i].content) {
                        const contentEntityIds = this.getEntitiesInContent(content[i].content);
                        for (const j in contentEntityIds.entities) {
                            if (entityIds.indexOf(contentEntityIds.entities[j]) === -1) {
                                entityIds.push(contentEntityIds.entities[j]);
                            }
                        }

                        for (const k in contentEntityIds.universalEntities) {
                            if (universalEntityIds.indexOf(contentEntityIds.universalEntities[k]) === -1) {
                                universalEntityIds.push(contentEntityIds.universalEntities[k]);
                            }
                        }
                    }
                }

                return {
                    entities: entityIds,
                    universalEntities: universalEntityIds
                };
            },

            setVersions(versions) {
                this.versions = versions;

                if (typeof versions[this.activeVersionIndex] === 'undefined') {
                    this.activeVersionIndex = 0;
                }
            },

            getVersions() {
                return this.versions;
            },

            setContent(content) {
                if (content === null || typeof content === 'undefined') {
                    content = [];
                }

                this.content = typeof content === 'string' ? JSON.parse(content) : content;
                this.$refs.content.changeContent(this.content);
                this.fetchModulePreviews();
            },

            confirmVersionChange(index) {
                swal({
                    title: this.localization.trans('version_change_confirm.title'),
                    text: this.localization.trans('version_change_confirm.text'),
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: this.localization.trans('version_change_confirm.cancel'),
                            visible: true
                        },
                        confirm: {
                            text: this.localization.trans('version_change_confirm.confirm'),
                            value: true
                        }
                    },
                    dangerMode: true
                })
                    .then(isConfirm => {
                        if (isConfirm) {
                            this.changeVersion(index);
                        }
                    });
            }
        },

        computed: {
            contentEditMode() {
                return this.mode === MODES.CONTENT;
            },

            layoutEditMode() {
                return this.mode === MODES.LAYOUT;
            },

            activeVersion() {
                return this.versions[this.activeVersionIndex];
            },

            rootItem() {
                return {
                    type: 'root',
                    content: this.content
                };
            }
        },

        watch: {
            activeLayout(layout) {
                this.$root.$emit(EVENTS.LAYOUT_CHANGED, layout);
            }
        }
    }
</script>

<style lang="scss">
    $activeColor: #26a69a;
    $grayColor: #9f9f9f;
    $darkColor: #2f2f2f;
    $backgroundGray: #f7f8f7;
    $backgroundDarkGray: #ebebeb;
    $blueColor: #26a69a;

    .btn-grideditor {
        background: transparent;
        color: $grayColor;

        &.active, &:hover, &:active, &:focus {
            background: transparent;
            box-shadow: none;
            color: $activeColor;
        }
    }

    .grideditor {
        color: $darkColor;

        ._grid-move {
            cursor: grab;
        }
        ._grid-move:active {
            cursor: grabbing;
        }

        .control-panel {
            margin-bottom: 10px;
            clear: both;

            .btn-grideditor {
                color: $darkColor;
                padding-left: 45px;

                > i { // fixed
                    font-size: 1.5em;
                    position: absolute;
                    display: inline-block;
                    width: 40px;
                    left: 0;
                }

                &.btn-with-info > i {
                    font-size: 2.5em;
                }

                > small { // fixed
                    color: $grayColor;
                    display: block;
                    text-align: left;
                }

                &:not(.btn-with-info) {
                    padding-top: 10px;
                }

                &.slim {
                    padding-left: 35px;

                    > i {
                        bottom: 2px;
                    }
                }

                &.version {
                    border: 1px solid $grayColor;
                    border-radius: 3px;
                    text-align: left;
                    padding: 5px 50px 5px 40px;

                    > i {
                        color: $grayColor;
                        left: 15px;
                        font-size: 1.5em;
                        top: 10px;
                    }

                    .caret {
                        float: right;
                        position: absolute;
                        top: 15px;
                        right: 10px;
                    }

                    & + .dropdown-menu > li > a {
                        font-weight: bold;

                        > small {
                            color: $grayColor;
                            display: block;
                            font-weight: normal;
                        }
                    }
                }

                &:not([disabled]):hover, &:not([disabled]):active, &:not([disabled]).active {
                    color: $activeColor;
                }
            }
        }

        .new-row {
            border: 1px dashed $grayColor;
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;

            .plus {
                font-size: 2.5em;
                position: absolute;
                top: -5px;
                margin-left: -25px;
            }

            &:hover, &:active, &:focus, &.active {
                border-color: $activeColor;
            }
        }

        // GRID CONTENT
        .grid {
            // TOOLBAR
            ._grid-toolbar {
                display: block;
                background: white;
                list-style: none;
                padding: 0;

                > li {
                    display: inline-block;
                }

                .btn-grideditor {
                    padding: 5px;
                    overflow: hidden;
                }
            }

            // BOTTOM CONTROLS OF ROW OR COLUMN
            ._grid-bottom-controls {
                position: absolute;
                bottom: 0;
                width: 100%;

                .btn-grideditor {
                    padding-left: 30px;
                    .plus {
                        font-size: 2em;
                        position: absolute;
                        margin: -10px 0px 0px -20px;
                    }
                }
            }

            ._grid-content {
                min-height: 140px;
                padding: 5px 2px 30px;

                &:before, &:after {
                    content: " ";
                    display: table;
                }

                &:after {
                    clear: both;
                }
            }

            ._grid-container, .row, ._grid-column {
                margin: 5px 5px 5px 30px;
                position: relative;
                min-height: 140px;

                > ._grid-toolbar {
                    position: absolute;
                    width: 25px;
                    top: 0;
                    left: -25px;

                    > li > .btn-grideditor {
                        padding-left: 7.5px;
                    }
                }
            }

            ._grid-container {
                background: $backgroundDarkGray;

                // TOOLBAR OF ROW
                > ._grid-toolbar {
                    background: $backgroundDarkGray;
                }
            }

            // ROW
            .row {
                background: $backgroundGray;

                > ._grid-content {
                    padding-right: 0;
                }

                // TOOLBAR OF ROW
                > ._grid-toolbar {
                    background: $backgroundGray;
                }
            }

            // COLUMN
            ._grid-column {
                background: white;
                margin-right: 5px;

                ._grid-column-resize {
                    height: 25px;
                    width: 8px;
                    border-right: 4px double #9f9f9f;
                    position: absolute;
                    right: 3px;
                    top: calc(50% - 13px);
                    cursor: col-resize;
                    padding: 4px 18px 0 0;

                    &:hover {
                        border-color: $activeColor;
                    }

                    &:not(:empty) {
                        background: white;
                    }
                }
            }

            // MODULE
            ._grid-module {
                border: 1px solid $blueColor;
                background: white;
                margin: 0 5px;

                ._grid-module-header {
                    > span {
                        background: $blueColor;
                        color: white;
                        padding: 5px 12px;
                        display: inline-block;

                        > i {
                            margin-right: 5px;
                        }
                    }

                    > ._grid-toolbar {
                        float: right;
                    }
                }

                ._grid-module-preview {
                    padding: 20px;
                    position: relative;

                    > ._grid-module-overlap {
                        background: rgba(255, 255, 255, 0.8);
                        position: absolute;
                        width: 100%;
                        height: 100%;
                        top: 0;
                        left: 0;

                        > button {
                            margin: 20px auto 0;
                            display: block;
                        }
                    }
                }
            }

            @for $i from 1 through 12 {
                .col-xs-#{$i} {
                    width: calc(#{100 * ($i / 12)}% - 40px) !important;
                    padding: 0 5px 0 0;
                }
            }
        }
    }

    body._grid_col_resizing {
        cursor: col-resize !important;

        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;

        -o-user-select: none;
        user-select: none;

        .btn-grideditor {
            cursor: col-resize !important;
        }

        ._grid-module-overlap {
            display: none;
        }
    }

    ._grid-module-list-icon {
        text-align: center;
        margin-bottom: 25px;
        display: block;
        color: #2F2F2F;

        .preview {
            margin-bottom: 10px;
            padding: 12px 0;
            width: 100%;
            border: 1px dashed #c2c2c2;
            background-position: center !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;

            &:hover {
                border: 1px solid $blueColor;
            }

            > i.fa {
                font-size: 2em;
            }
        }

        small {
            display: block;
        }

        &:hover {
            color: $blueColor;
            .preview {
                border: 1px solid $blueColor;
            }
        }
    }

    .version-switch > .dropdown-menu {
        max-height: 300px;
        overflow-x: auto;
    }

    // SORTABLE
    .grideditor {
        .draggable--dropzone {
            min-height: 10px;
            border: 1px dashed transparent;
            clear: both;
            margin: 1px 5px;
        }

        .draggable--dropzone.size-adjusting {
            clear: none;
        }

        ._grid-receive-column > .draggable--dropzone.size-adjusting:not(.col-xs-12) {
            height: 140px;
        }

        .draggable--dropzone.col-xs-0 {
            width: 10px;
            display: inline-block;
            float: left;
            margin: 0;

            & + ._grid-column {
                margin-left: 25px;
            }
        }

        @for $i from 1 through 12 {
            .draggable--dropzone.col-xs-#{$i} {
                width: calc(#{100 * ($i / 12)}% - 10px) !important;
                padding: 0 5px 0 0;
            }
        }

        .is-dragged {
            display: block !important;
        }
    }

    .container-helper, .row-helper, .column-helper, .module-helper {
        padding: 10px;
        background: rgba(38, 166, 154, 0.2);
        width: 250px;
        border: 1px dashed $blueColor;
        margin-top: -25px;
    }

    .container-helper {
        background: rgba(235, 235, 235, 0.8);
    }

    .row-helper {
        background: rgba(247, 248, 247, 0.8);
    }

    .column-helper {
        background: rgba(255, 255, 255, 0.8);
    }

    .grideditor {
        .draggable--dropzone.draggable--visible {
            border-color: $blueColor;

            &:hover {
                cursor: copy;
                background: repeating-linear-gradient(
                        45deg,
                        #26a69a,
                        #26a69a 10px,
                        transparent 10px,
                        transparent 20px
                );
                border: 1px solid #26a69a;
            }
        }
    }
</style>
