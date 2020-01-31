/* global Vue, $, Switchery */

const options = window.gemLinkConfigurationFormOptions();

const LinkAttribute = Vue.extend({
    props: ['attribute'],
    template: '#attribute-row'
});

new Vue({
    el: '#gem-link-configuration-form',
    data: function () {
        return {
            customUrl: null,
            model: '',
            attributes: options.attributes,
            customUrlSwitchery: null
        };
    },
    components: {
        'link-attribute': LinkAttribute
    },
    methods: {
        addAttributeRow: function () {
            this.attributes.push({
                name: '',
                value: ''
            });
        },
        fillForm: function (data) {
            this.attributes = [];

            // Attributes
            if (!data.attribute_key || !data.attribute_value) {
                return false;
            }

            for (var i in data.attribute_key) {
                if (Object.prototype.hasOwnProperty.call(data.attribute_key, i)
                    && Object.prototype.hasOwnProperty.call(data.attribute_value, i)) {
                    this.attributes.push({
                        name: data.attribute_key[i],
                        value: data.attribute_value[i]
                    });
                }
            }
        },
        removeAttribute: function (index) {
            this.attributes.splice(index, 1);
        }
    },
    created: function () {
        this.customUrl = document.getElementById('ml-custom-url-input').checked;
    },
    mounted: function () {
        // Switchery
        this.customUrlSwitchery = new Switchery(document.getElementById('ml-custom-url-input'));

        // Form fill
        $(this.$refs.form).trigger('admin:form-fill-ready', this.fillForm);
    }
});
