import AbstractEvent from '../../shared/AbstractEvent';

/**
 * Base drag event
 * @class DragEvent
 * @module DragEvent
 * @extends AbstractEvent
 */
export class DragEvent extends AbstractEvent {
    /**
     * Read-only type
     * @abstract
     * @return {String}
     */
    static get type() {
        return 'drag';
    }

    /**
     * Sensor event
     * @property sensorEvent
     * @type {SensorEvent}
     * @readonly
     */
    get sensorEvent() {
        return this.data.sensorEvent;
    }

    /**
     * Item type
     * @property itemType
     * @type {String}
     * @readonly
     */
    get itemType() {
        return this.data.itemType;
    }

    /**
     * Source
     * @property source
     * @type {Array}
     * @readonly
     */
    get source() {
        return this.data.source;
    }

    /**
     * Item
     * @property item
     * @type {String|Object|Number}
     * @readonly
     */
    get item() {
        return this.data.item;
    }

    /**
     * Original event that triggered sensor event
     * @property originalEvent
     * @type {Event}
     * @readonly
     */
    get originalEvent() {
        if (this.sensorEvent) {
            return this.sensorEvent.originalEvent;
        }

        return null;
    }
}

/**
 * Drag start event
 * @class DragStartEvent
 * @module DragStartEvent
 * @extends DragEvent
 */
export class DragStartEvent extends DragEvent {
    /**
     * Read-only cancelable
     * @abstract
     * @return {Boolean}
     */
    get cancelable() {
        return true;
    }

    /**
     * Read-only type
     * @abstract
     * @return {String}
     */
    static get type() {
        return 'drag:start';
    }

    /**
     * Item path.
     * @property itemPath
     * @type {String}
     * @readonly
     */
    get itemPath() {
        return this.data.itemPath;
    }
}

/**
 * Drag stop event
 * @class DragStopEvent
 * @module DragStopEvent
 * @extends DragEvent
 */
export class DragStopEvent extends DragEvent {
    /**
     * Read-only type
     * @abstract
     * @return {String}
     */
    static get type() {
        return 'drag:stop';
    }

    /**
     * Target
     * @property target
     * @type {HTMLElement}
     * @readonly
     */
    get target() {
        return this.data.target;
    }
}
