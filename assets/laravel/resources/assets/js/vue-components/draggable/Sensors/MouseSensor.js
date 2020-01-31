import {DragStartSensorEvent, DragMoveSensorEvent, DragStopSensorEvent} from './SensorEvent';

const onContextMenuWhileDragging = Symbol('onContextMenuWhileDragging');
const onMouseDown = Symbol('onMouseDown');
const onMouseMove = Symbol('onMouseMove');
const onMouseUp = Symbol('onMouseUp');

/**
 * This sensor picks up native browser mouse events and dictates drag operations
 * @class MouseSensor
 * @module MouseSensor
 */
export default class MouseSensor {
    /**
     * MouseSensor constructor.
     * @constructs MouseSensor
     * @param {HTMLElement} container - Container
     * @param {String} handleSelector - Selector of a handle
     */
    constructor(container, handleSelector) {
        /**
         * Reference to the container.
         * @property container
         * @type {HTMLElement}
         */
        this.container = container;

        /**
         * Selector of the handle.
         * @property handleSelector
         * @type {String}
         */
        this.handleSelector = handleSelector;

        /**
         * Indicates if mouse button is still down.
         * @property mouseDown
         * @type {Boolean}
         */
        this.mouseDown = false;

        /**
         * Indicates if context menu has been opened during drag operation
         * @property openedContextMenu
         * @type {Boolean}
         */
        this.openedContextMenu = false;

        this[onContextMenuWhileDragging] = this[onContextMenuWhileDragging].bind(this);
        this[onMouseDown] = this[onMouseDown].bind(this);
        this[onMouseMove] = this[onMouseMove].bind(this);
        this[onMouseUp] = this[onMouseUp].bind(this);
    }

    /**
     * Attaches sensors event listeners to the DOM
     */
    attach() {
        const selector = this.container.querySelectorAll(this.handleSelector);

        if (!selector.length) {
            console.error(`Handle with selector "${this.handleSelector}" not found in this container.`);
            return;
        }

        selector[0].addEventListener('mousedown', this[onMouseDown], true);
    }

    /**
     * Detaches sensors event listeners to the DOM
     */
    detach() {
        const selector = this.container.querySelectorAll(this.handleSelector);

        if (!selector.length) {
            console.error(`Handle with selector "${this.handleSelector}" not found in this container.`);
            return;
        }

        selector[0].removeEventListener('mousedown', this[onMouseDown], true);
    }

    /**
     * Triggers event on target element
     * @param {SensorEvent} sensorEvent - Sensor event to trigger
     */
    trigger(sensorEvent) {
        const event = document.createEvent('Event');
        event.detail = sensorEvent;
        event.initEvent(sensorEvent.type, true, true);
        this.container.dispatchEvent(event);
        this.lastEvent = sensorEvent;

        return sensorEvent;
    }

    /**
     * Mouse down handler
     * @private
     * @param {Event} event - Mouse down event
     */
    [onMouseDown](event) {
        event.stopPropagation();

        if (event.button !== 0 || event.ctrlKey || event.metaKey) {
            return;
        }

        const target = event.target;
        const handle = event.currentTarget;

        if (!handle) {
            return;
        }

        document.addEventListener('mouseup', this[onMouseUp]);
        document.addEventListener('dragstart', preventNativeDragStart);

        this.mouseDown = true;

        const dragStartEvent = new DragStartSensorEvent({
            clientX: event.clientX,
            clientY: event.clientY,
            target,
            handle,
            originalEvent: event,
        });

        this.trigger(dragStartEvent);

        this.dragging = !dragStartEvent.canceled();

        if (this.dragging) {
            document.addEventListener('contextmenu', this[onContextMenuWhileDragging]);
            document.addEventListener('mousemove', this[onMouseMove]);
        }
    }

    /**
     * Mouse move handler
     * @private
     * @param {Event} event - Mouse move event
     */
    [onMouseMove](event) {
        if (!this.dragging) {
            return;
        }

        const target = document.elementFromPoint(event.clientX, event.clientY);

        const dragMoveEvent = new DragMoveSensorEvent({
            clientX: event.clientX,
            clientY: event.clientY,
            target,
            originalEvent: event,
        });

        this.trigger(dragMoveEvent);
    }

    /**
     * Mouse up handler
     * @private
     * @param {Event} event - Mouse up event
     */
    [onMouseUp](event) {
        this.mouseDown = Boolean(this.openedContextMenu);

        if (this.openedContextMenu) {
            this.openedContextMenu = false;
            return;
        }

        document.removeEventListener('mouseup', this[onMouseUp]);
        document.removeEventListener('dragstart', preventNativeDragStart);

        if (!this.dragging) {
            return;
        }

        const target = document.elementFromPoint(event.clientX, event.clientY);

        const dragStopEvent = new DragStopSensorEvent({
            clientX: event.clientX,
            clientY: event.clientY,
            target,
            originalEvent: event,
        });

        this.trigger(dragStopEvent);

        document.removeEventListener('contextmenu', this[onContextMenuWhileDragging]);
        document.removeEventListener('mousemove', this[onMouseMove]);

        this.dragging = false;
    }

    /**
     * Context menu handler
     * @private
     * @param {Event} event - Context menu event
     */
    [onContextMenuWhileDragging](event) {
        event.preventDefault();
        this.openedContextMenu = true;
    }
}

function preventNativeDragStart(event) {
    event.preventDefault();
}
