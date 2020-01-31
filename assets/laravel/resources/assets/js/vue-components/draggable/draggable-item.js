import 'element-qsa-scope'; // polyfill for :scope selector
import MouseSensor from './Sensors/MouseSensor';
import {DragStartSensorEvent, DragMoveSensorEvent, DragStopSensorEvent} from './Sensors/SensorEvent';
import {DragStartEvent, DragStopEvent} from './Events';

const CLASSES = {
    'body:dragging': 'draggable--is-dragging'
};

const Renderer = Vue.extend({
    props: ['node'],
    render(h, context) {
        return this.node ? this.node : ''
    }
});

export default {
    props: {
        handle: {
            type: String
        },
        type: {
            type: String
        },
        source: {
            type: Array,
            default: null
        },
        item: {
            type: [Object, Number, String],
            required: true
        },
        path: {
            type: String,
            default: null
        }
    },

    data () {
        return {
            /**
             * @type {MouseSensor}
             * @property mouseSensor
             */
            mouseSensor: null,

            /**
             * @type {HTMLElement}
             * @property helper
             */
            helper: null
        };
    },

    render () {
        return this.$slots.default[0];
    },

    methods: {
        initializeSensors() {
            this.mouseSensor = new MouseSensor(this.$el, this.handle);
            this.mouseSensor.attach();

            this.$el.addEventListener(DragStartSensorEvent.type, this.dragStart);
        },

        initializeHelper() {
            const renderer = new Renderer({
                propsData: {
                    node: this.$slots.helper[0]
                }
            });
            renderer.$mount();
            this.helper = renderer.$el;

            document.body.appendChild(this.helper);
            this.helper.style.position = 'absolute';
            this.helper.style.zIndex = '9999';
        },

        destroyHelper() {
            if (this.helper === null) {
                return;
            }

            document.body.removeChild(this.helper);
            this.helper = null;
        },

        setHelperPosition(mouseX, mouseY) {
            const doc = document.documentElement;
            const left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
            const top = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
            this.helper.style.left = (mouseX + left + 5) + 'px';
            this.helper.style.top = (mouseY + top + 5) + 'px';
        },

        /**
         * Dragging started.
         * @param {Event} event
         * @param {DragStartSensorEvent} event.detail
         */
        dragStart (event) {
            event.stopPropagation();
            const sensorEvent = event.detail;

            document.body.classList.add(CLASSES['body:dragging']);
            applyUserSelect(document.body, 'none');

            this.initializeHelper();
            this.setHelperPosition(sensorEvent.clientX, sensorEvent.clientY);

            const dragEvent = new DragStartEvent({
                source: this.source,
                item: this.item,
                itemType: this.type,
                itemPath: this.path,
                sensorEvent
            });

            this.$el.addEventListener(DragMoveSensorEvent.type, this.dragMove);
            this.$el.addEventListener(DragStopSensorEvent.type, this.dragStop);

            this.globalEmit(dragEvent);
        },

        /**
         * Drag move.
         * @param {Event} event
         * @param {DragMoveSensorEvent} event.detail
         */
        dragMove (event) {
            event.stopPropagation();

            const sensorEvent = event.detail;
            this.setHelperPosition(sensorEvent.clientX, sensorEvent.clientY);
        },

        /**
         * Dragging stopped.
         * @param {Event} event
         * @param {DragStopSensorEvent} event.detail
         */
        dragStop (event) {
            event.stopPropagation();
            const sensorEvent = event.detail;

            const dragStopEvent = new DragStopEvent({
                source: this.source,
                item: this.item,
                itemType: this.type,
                sensorEvent: sensorEvent,
                target: sensorEvent.target
            });

            this.globalEmit(dragStopEvent);

            this.destroyHelper();

            this.$el.removeEventListener(DragMoveSensorEvent.type, this.dragMove);
            this.$el.removeEventListener(DragStopSensorEvent.type, this.dragStop);

            document.body.classList.remove(CLASSES['body:dragging']);
            applyUserSelect(document.body, '');
        },

        /**
         * Emits event on body element
         * @param {DragEvent} dragEvent - Drag event to trigger
         */
        globalEmit(dragEvent) {
            const event = document.createEvent('Event');
            event.detail = dragEvent;
            event.initEvent(dragEvent.type, true, true);
            document.body.dispatchEvent(event);

            return dragEvent;
        }
    },

    mounted() {
        this.initializeSensors();
    },

    beforeDestroy() {
        if (this.mouseSensor) {
            this.mouseSensor.detach();
        }

        this.$el.removeEventListener(DragStartSensorEvent.type, this.dragStart);
        this.destroyHelper();
    }
}

function applyUserSelect(element, value) {
    element.style.webkitUserSelect = value;
    element.style.mozUserSelect = value;
    element.style.msUserSelect = value;
    element.style.oUserSelect = value;
    element.style.userSelect = value;
}
