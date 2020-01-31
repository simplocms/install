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
            texts: {
                type: Object,
                required: true
            },
            action: {
                type: String,
                validator(value) {
                    return ['delete', 'post', 'emit'].indexOf(value) !== -1;
                },
                required: true
            },
            link: {
                type: String,
                default: '/'
            },
            icon: {
                type: String,
                default: 'warning'
            },
            dangerMode: {
                type: Boolean,
                default: false
            }
        },

        methods: {
            clickHandler(event) {
                event.preventDefault();
                swal({
                    title: this.texts.title,
                    text: this.texts.text,
                    icon: this.icon,
                    buttons: {
                        cancel: {
                            text: this.texts.cancel,
                            visible: true
                        },
                        confirm: {
                            text: this.texts.confirm,
                            value: true
                        }
                    },
                    dangerMode: this.dangerMode || this.action === 'delete'
                })
                    .then(isConfirm => {
                        if (isConfirm) {
                            this.triggerAction();
                        }
                    });
            },

            triggerAction() {
                switch (this.action) {
                    case 'delete':
                        axios.delete(this.link)
                            .then(response => {
                                this.processResponse(response);
                                this.$emit('done');
                            })
                            .catch(thrown => {
                                this.$emit('error', thrown);
                            });
                        break;
                    case 'post':
                        axios.post(this.link)
                            .then(response => {
                                this.processResponse(response);
                                this.$emit('done');
                            })
                            .catch(thrown => {
                                this.$emit('error', thrown);
                            });
                        break;
                    case 'emit':
                        this.$emit('confirm');
                        break;
                }
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
