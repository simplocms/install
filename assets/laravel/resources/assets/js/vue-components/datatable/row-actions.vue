<template>
    <ul class="icons-list" v-if="controls.length">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bars"></i>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li v-for="(control, index) in controls"
                    :key="index"
                >
                    <v-confirm-action v-if="control.isDelete"
                                      action="delete"
                                      :texts="control.confirmOptions"
                                      :link="control.url"
                    >
                        <i v-if="control.icon" :class="['fa fa-fw fa-' + control.icon]"></i>
                        {{ control.text }}
                    </v-confirm-action>
                    <a v-else
                       :href="control.url"
                       :target="control.target"
                       @click="onClick($event, control)"
                    >
                        <i v-if="control.icon" :class="['fa fa-fw fa-' + control.icon]"></i>
                        {{ control.text }}
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            controls: {
                type: Array,
                required: true
            }
        },

        methods: {
            onClick($event, control) {
                if (control.isAutomaticPost) {
                    $event.preventDefault();

                    axios.post($event.target.href)
                        .then(response => {
                            location.reload();
                        })
                        .catch(thrown => {
                            $.jGrowl(thrown.response.data.message, {
                                header: this.$root.localization.trans('flash_level.danger'),
                                theme: 'bg-danger'
                            });
                        });
                }

                if (control.emits) {
                    $event.preventDefault();

                    const event = {
                        type: control.emits,
                        control
                    };

                    this.$emit('emits', event);
                }
            }
        }
    };

</script>
