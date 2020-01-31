Vue.component('users-form', {
    mounted () {
        $('.switchery').add('#input-enabled').each(function () {
            new Switchery(this);
        });

        $('.maxlength').maxlength();
    }
});