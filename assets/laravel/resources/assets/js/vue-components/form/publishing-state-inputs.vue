<template>
    <div>
        <v-radio v-model="form.state" :input-value="publishingStates.CONCEPT.value">
            {{ publishingStates.CONCEPT.label }}
        </v-radio>

        <v-radio v-model="form.state" :input-value="publishingStates.PUBLISHED.value">
            {{ publishingStates.PUBLISHED.label }}
        </v-radio>

        <div v-if="form.state === publishingStates.PUBLISHED.value"
             style="padding-left: 28px"
             class="text-size-small"
        >
            <div>{{ localization.trans('since_text') }} {{ form.publish_at_date }} {{ form.publish_at_time }}</div>
            <div v-if="form.set_unpublish_at && form.unpublish_at_date">
                {{ localization.trans('until_text') }} {{ form.unpublish_at_date }} {{ form.unpublish_at_time }}
            </div>

            <!-- Popover -->
            <popover :title="localization.trans('title')">
                <!-- Toggle link -->
                <template slot="toggle" slot-scope="{openHandler}">
                    <a href="#" class="abbr-link" @click.prevent="openHandler">
                        {{ localization.trans('toggle_text') }}
                    </a>
                </template>

                <div class="publishing-inputs-content">
                    <!-- Publish at -->
                    <v-form-group>
                        <label class="display-block">{{ localization.trans('label_publish_at') }}</label>

                        <a href="#"
                           @click.prevent.stop="insertCurrentPublishAt"
                           class="insert-current-time text-size-small"
                        >
                            {{ localization.trans('btn_set_current_time') }}
                        </a>

                        <!-- Date -->
                        <div class="input-group input-group-date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <date-picker class="form-control popover-date-picker"
                                         name="publish_at_date"
                                         :placeholder="localization.trans('placeholder_date')"
                                         v-model="form.publish_at_date"
                            ></date-picker>
                        </div>

                        <!-- Time -->
                        <div class="input-group input-group-time">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            <time-picker class="form-control popover-time-picker"
                                         name="publish_at_time"
                                         v-model="form.publish_at_time"
                                         :placeholder="localization.trans('placeholder_time')"
                            ></time-picker>
                        </div>
                    </v-form-group>

                    <!-- Set unpublish at? -->
                    <v-checkbox-switch v-model="form.set_unpublish_at"
                                       name="set_unpublish_at"
                                       class="mt-20"
                    >
                        {{ localization.trans('label_set_unpublish_at') }}
                    </v-checkbox-switch>

                    <!-- Unpublish at -->
                    <div class="collapse mt-20"
                         :class="[form.set_unpublish_at ? 'in' : 'out']"
                    >
                        <v-form-group>
                            <label class="display-block">{{ localization.trans('label_unpublish_at') }}</label>

                            <a href="#"
                               @click.prevent.stop="insertCurrentUnpublishAt"
                               class="insert-current-time text-size-small"
                            >
                                {{ localization.trans('btn_set_current_time') }}
                            </a>

                            <!-- Date -->
                            <div class="input-group input-group-date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <date-picker class="form-control popover-date-picker"
                                             name="unpublish_at_date"
                                             :placeholder="localization.trans('placeholder_date')"
                                             v-model="form.unpublish_at_date"
                                ></date-picker>
                            </div>

                            <!-- Time -->
                            <div class="input-group input-group-time">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                <time-picker class="form-control popover-time-picker"
                                             name="unpublish_at_time"
                                             v-model="form.unpublish_at_time"
                                             :placeholder="localization.trans('placeholder_time')"
                                ></time-picker>
                            </div>
                        </v-form-group>
                    </div>
                </div>
            </popover>

        </div>

        <v-radio v-model="form.state" :input-value="publishingStates.UNPUBLISHED.value">
            {{ publishingStates.UNPUBLISHED.label }}
        </v-radio>
    </div>
</template>

<script>
    import LocalizationMixin from '../../vue-mixins/localization';
    import Popover from '../../vue-components/popover';
    import DatePicker from '../../vue-components/date-picker';
    import TimePicker from '../../vue-components/time-picker';

    export default {
        mixins: [LocalizationMixin],

        data() {
            return {};
        },

        components: {
            'popover': Popover,
            'date-picker': DatePicker,
            'time-picker': TimePicker,
        },

        props: {
            /** @type {Object} */
            publishingStates: Object,
            /** @type {Form} */
            form: {
                type: Object,
                required: true
            }
        },

        methods: {
            insertCurrentPublishAt() {
                const date = new Date();
                this.form.publish_at_date = this.dateToString(date);
                this.form.publish_at_time = this.timeToString(date);
            },

            insertCurrentUnpublishAt() {
                const date = new Date();
                this.form.unpublish_at_date = this.dateToString(date);
                this.form.unpublish_at_time = this.timeToString(date);
            },

            dateToString(date) {
                const month = date.getMonth() + 1;
                const day = date.getDate();
                let dateString = (("" + day).length < 2 ? "0" : "") + day + ".";
                dateString += (("" + month).length < 2 ? "0" : "") + month + ".";
                dateString += date.getFullYear();
                return dateString;
            },

            timeToString(time) {
                const hours = time.getHours() + 1;
                const minutes = time.getMinutes();
                let timeString = (("" + hours).length < 2 ? "0" : "") + hours + ":";
                timeString += (("" + minutes).length < 2 ? "0" : "") + minutes;
                return timeString;
            }
        }
    }
</script>

<style>
    .popover-date-picker + .picker {
        right: 0;
        min-width: 250px;
    }

    .popover-time-picker + .picker {
        right: 0;
    }

    .input-group-date {
        width: 140px;
    }

    .input-group-time {
        width: 100px;
    }

    .publishing-inputs-content {
        width: 250px;
    }

    .insert-current-time {
        position: absolute;
        right: 0;
        top: 3px;
    }
</style>
