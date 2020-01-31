const options = window.pageLanguageOptions();

Vue.component('page-languages', {
    data () {
        return {
            urlTypes: options.urlTypes
        };
    },
    mounted () {
        $(".styled").uniform({
            radioClass: 'choice'
        }).on('change', event => {
            if (event.target.type !== 'radio') {
                return;
            }

            const isTypeDirectory = Number(event.target.value) === this.urlTypes.directory;
            $('#default-language-input').toggle(isTypeDirectory && event.target.checked);
        });
    }
});