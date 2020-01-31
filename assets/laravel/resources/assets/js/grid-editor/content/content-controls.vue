<template>
    <ul class="_grid-toolbar">
        <li>
            <div class="btn btn-grideditor _grid-move" :title="localization.trans('content_controls.move')">
                <i class="fa fa-arrows"></i>
            </div>
        </li>
        <li>
            <button class="btn btn-grideditor" :title="localization.trans('content_controls.settings')" type="button"
                @click.prevent="$emit('show-settings')"
            >
                <i class="fa fa-cog"></i>
            </button>
        </li>
        <li>
            <button class="btn btn-grideditor" :title="localization.trans('content_controls.delete')" type="button"
                @click.prevent="$emit('remove')"
            >
                <i class="fa fa-trash-o"></i>
            </button>
        </li>
        <li>
            <button class="btn btn-grideditor" :title="localization.trans('content_controls.duplicate')" type="button"
                @click.prevent="$emit('clone')"
            >
                <i class="fa fa-spinner fa-spin" v-if="isCloning"></i>
                <i class="fa fa-copy" v-else></i>
            </button>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            isCloning: Boolean,
            localization: Object
        }
    }
</script>
