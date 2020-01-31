<script>
    export default {
        data() {
            return {};
        },

        props: {
            tag: {
                type: String,
                default: 'a'
            },
            url: {
                type: String,
                required: true
            },
        },

        methods: {
            clickHandler(event) {
                event.stopPropagation();
                event.preventDefault();

                axios.post(this.url)
                    .then(response => {
                        this.processResponse(response);
                        this.$emit('done');
                    })
                    .catch(thrown => {
                        this.$emit('error', thrown);
                    });
            },

            processResponse(response) {
                if (response.data.refresh) {
                    location.reload();
                }
            }
        },

        computed: {
            tagAttributes() {
                switch (this.tag) {
                    case 'a':
                        return {
                            href: '#'
                        };
                    default:
                        return {};
                }
            }
        },

        render: function (createElement) {
            return createElement(
                this.tag,
                {
                    on: {
                        click: this.clickHandler
                    },
                    attrs: this.tagAttributes,
                },
                this.$slots.default
            )
        },
    }
</script>
