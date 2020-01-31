export default {
    props: {
        trans: {
            type: Object,
            required: true
        },
    },

    computed: {
        localization() {
            return new Localization(this.trans);
        },
    }
};
