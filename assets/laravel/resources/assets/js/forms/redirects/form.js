import Form from '../../vendor/Form';

Vue.component('redirects-form', {
    data() {
        return {
            form: new Form({...this.redirect})
        };
    },

    props: {
        redirect: {
            type: Object,
            required: true
        }
    },
});
