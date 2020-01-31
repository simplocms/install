<script>
    import DynamicFormFieldMixin from '../../../../resources/assets/js/vue-mixins/dynamic-form-field';

    export default {
        mixins: [DynamicFormFieldMixin],
        computed: {
            fieldName() {
                return 'variables.' + this.field.name;
            }
        }
    }
</script>
