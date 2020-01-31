<template>
    <transition @enter="enter" @before-leave="beforeLeave" mode="out-in">
        <component :is="tag"
                   :id="id"
                   role="tabpanel"
                   :class="['tab-pane', 'has-padding', {show, fade, disabled, active: localActive}]"
                   :aria-hidden="localActive ? 'false' : 'true'"
                   :aria-expanded="localActive ? 'true' : 'false'"
                   :aria-labelledby="controlledBy || null"
                   v-if="localActive || !lazy"
                   v-show="localActive || lazy"
                   ref="panel"
        >
             <slot></slot>
        </component>
    </transition>
</template>

<script>
    export default {
        methods: {
            enter() {
                this.show = true;
				this.$emit('shown');
            },
            beforeLeave() {
                this.show = false;
            }
        },
        data() {
            return {
                fade: false,
                localActive: false,
                lazy: true,
                show: false,
				tracked: true
            };
        },
        computed: {
            controlledBy() {
                return this.buttonId || (this.id ? (this.id + '__BV_tab_button__') : null);
            },

			hasHashHref () {
				return this.href.length && this.href.substring(0, 1) === '#';
			},

			isTrackable () {
				return this.tracked && !this.disabled && this.visible && this.hasHashHref;
			}
        },
        watch: {
        	localActive (value) {
        		if (value && this.tracked && this.hasHashHref) {
					window.location.hash = this.href;
                }
            }
        },
        props: {
            id: {
                type: String,
                default: ''
            },
            tag: {
                type: String,
                default: 'div'
            },
            buttonId: {
                type: String,
                default: ''
            },
            title: {
                type: String,
                default: ''
            },
            headHtml: {
                type: String,
                default: null
            },
			disabled: {
				type: Boolean,
				default: false
			},
			visible: {
				type: Boolean,
				default: true
			},
            error: {
				type: Boolean,
				default: false
			},
            active: {
                type: Boolean,
                default: false
            },
            href: {
                type: String,
                default: '#'
            },
            isTracked: {
                type: Boolean,
                default: true
            }
        },
		updated () {
        	this.$emit('updated');
        },
    };

</script>
