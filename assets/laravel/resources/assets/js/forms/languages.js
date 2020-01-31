Vue.component('languages-form', {
    mounted () {
        new Switchery(document.getElementById('input-enabled'));
        $('.maxlength').maxlength();
    }
});