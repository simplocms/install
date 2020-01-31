<template>
    <div class='row'>
        <div class='col-xs-12'>
            <v-form :form="form"
                    method="POST"
                    :action="submitUrl"
            >
                <div class="panel panel-body">
                    <!-- Action button -->
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-sliders"></i> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" @click.prevent="clearTable">
                                    {{ localization.trans('btn_clear') }}
                                </a>
                            </li>
                            <li>
                                <a :href="importExample">
                                    {{ localization.trans('btn_import_example') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Import button -->
                    <button type="button"
                            @click.prevent="openFileInput"
                            class="btn btn-primary pull-right mb-15 mr-5"
                    >
                        <i class="fa fa-upload"></i>
                        {{ localization.trans('btn_import') }}
                    </button>
                    <input type="file" accept=".csv" ref="fileInput" v-show="false" @change="handleFileSelect"/>

                    <div class="alert alert-danger" style="clear: both" v-if="form.hasError('redirects')">
                        <i class="fa fa-exclamation-triangle"></i>
                        {{ form.getError('redirects') }}
                    </div>

                    <!-- Table -->
                    <table class="table table-striped" id="redirects-bulk-create-table">
                        <thead>
                        <tr>
                            <th>{{ localization.trans('table_columns.from') }}</th>
                            <th>{{ localization.trans('table_columns.to') }}</th>
                            <th width="130">{{ localization.trans('table_columns.status_code') }}</th>
                            <th width="80" class="text-center">
                                {{ localization.trans('table_columns.actions') }}
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(redirect, index) in form.redirects"
                            :key="index"
                        >
                            <td>
                                <v-form-group :error="form.getError(`redirects.${index}.from`)">
                                    <input v-model="redirect.from"
                                           type="text"
                                           maxlength="250"
                                           v-maxlength
                                           class="form-control"
                                    />
                                </v-form-group>
                            </td>
                            <td>
                                <v-form-group :error="form.getError(`redirects.${index}.to`)">
                                    <input v-model="redirect.to"
                                           type="text"
                                           maxlength="250"
                                           v-maxlength
                                           class="form-control"
                                    />
                                </v-form-group>
                            </td>
                            <td>
                                <v-form-group :error="form.getError(`redirects.${index}.status_code`)">
                                    <select v-model="redirect.status_code" class="form-control">
                                        <option v-for="(text, code) in statusCodes"
                                                :key="code"
                                                :value="code"
                                        >{{ text }}
                                        </option>
                                    </select>
                                </v-form-group>
                            </td>
                            <td class="text-center">
                                <a href="#" class="text-danger" @click.prevent="removeRow(index)">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-center p-10">
                                <a href="#" @click.prevent="addRow">
                                    <i class="fa fa-plus"></i>
                                    {{ localization.trans('btn_add_row') }}
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Form buttons -->
                <div class="form-group mt15">
                    <button type="submit" class="btn bg-teal-400 btn-labeled">
                        <b><i class="fa fa-save"></i></b>
                        {{ localization.trans('btn_save') }}
                    </button>
                    <a :href="backUrl" class='btn btn-default'>
                        {{ localization.trans('btn_cancel') }}
                    </a>
                </div>
            </v-form>
        </div>
    </div>
</template>

<script>
    import Form from '../../vendor/Form';
    import LocalizationMixin from '../../vue-mixins/localization';

    export default {
        mixins: [LocalizationMixin],
        data() {
            return {
                form: new Form({
                    redirects: []
                }),
                fallbackStatusCode: 301
            };
        },

        props: {
            submitUrl: String,
            backUrl: String,
            importExample: String,
            statusCodes: Object
        },

        methods: {
            openFileInput() {
                this.$refs.fileInput.click();
            },

            handleFileSelect(event) {
                if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
                    alert(this.$root.localization.trans('update_browser'));
                    return;
                }

                const file = event.target.files[0];
                const reader = new FileReader();

                reader.onload = event => {
                    this.readCsvFile(event.target.result);
                };

                reader.readAsText(file);
                this.$refs.fileInput.value = [];
            },

            readCsvFile(data) {
                this.form.clearError('redirects');
                data.split('\n').forEach((row, index) => {
                    if (index === 0) {
                        return;
                    }

                    const cols = row.split(';');
                    this.form.redirects.push({
                        'from': cols[0],
                        'to': cols[1],
                        'status_code': Number(cols[2] || 0) || this.fallbackStatusCode,
                    });
                });
            },

            removeRow(index) {
                this.form.redirects.splice(index, 1);
                this.form.clearErrors();
            },

            addRow() {
                this.form.redirects.push({
                    'from': '',
                    'to': '',
                    'status_code': this.fallbackStatusCode,
                });
                this.form.clearError('redirects');
            },

            clearTable() {
                this.form.redirects = [];
                this.form.clearErrors();
            }
        }
    }
</script>

<style scoped>
    #redirects-bulk-create-table tr > td {
        padding: 0 8px
    }

    #redirects-bulk-create-table .form-group {
        margin: 8px 0;
    }

    #redirects-bulk-create-table .form-group.has-error {
        margin-bottom: 0;
    }
</style>
