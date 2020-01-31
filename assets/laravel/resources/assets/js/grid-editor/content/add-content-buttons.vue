<template>
    <div class="row">
        <div class="col-md-4">
            <button type="button"
                    class="btn btn-grideditor new-row"
                    @click.prevent="$emit('add-container')"
            >
                <span class="plus">+</span> {{ localization.trans('content_buttons.add_container') }}
            </button>
        </div>
        <div class="col-md-4">
            <button type="button"
                    class="btn btn-grideditor new-row"
                    @click.prevent="$emit('add-row')"
            >
                <span class="plus">+</span> {{ localization.trans('content_buttons.add_row') }}
            </button>
        </div>
        <div class="col-md-4">
            <button type="button"
                    class="btn btn-grideditor new-row"
                    @click.prevent="$emit('add-module')"
            >
                <span class="plus">+</span> {{ localization.trans('content_buttons.add_module') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            localization: Object
        }
    }
</script>
