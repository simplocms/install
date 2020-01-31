import Popper from 'popper.js';

// Key which we use to store maxlength label object on element
const DATA_KEY = '__MaxLength__';

/**
 * Apply MaxLength to the given element.
 * @param {HTMLElement} el
 * @param {object} bindings
 * @param {VNode} vnode
 */
function applyMaxLength(el, bindings, vnode) {
    const config = {};

    if (typeof bindings.value === 'string' || typeof bindings.value === 'function') {
        config.maxLength = Number(bindings.value);
    }

    if (el[DATA_KEY]) {
        el[DATA_KEY].updateConfig(config)
    } else {
        el[DATA_KEY] = new MaxLength(el, config, vnode.context.$root)
    }
}

export default {
    bind(el, bindings, vnode) {
        applyMaxLength(el, bindings, vnode);
    },

    inserted(el, bindings, vnode) {
        applyMaxLength(el, bindings, vnode);
    },

    update(el, bindings, vnode) {
        if (bindings.value !== bindings.oldValue) {
            applyMaxLength(el, bindings, vnode);
        }
    },

    componentUpdated(el, bindings, vnode) {
        if (bindings.value !== bindings.oldValue) {
            applyMaxLength(el, bindings, vnode);
        }
    },

    unbind(el) {
        if (el[DATA_KEY]) {
            el[DATA_KEY].destroy();
            el[DATA_KEY] = null;
            delete el[DATA_KEY];
        }
    }
}

const STATE = {
    SHOW: 'show',
    OUT: 'out'
};

const DEFAULT_CONFIG = {
    template: '<span class="badge badge-secondary" style="display:block;position:absolute;white-space:nowrap;z-index: 10000"></span>',
    trigger: 'focus',
};

let NEXTID = 1;

/*
 * MaxLength Class definition
 */
class MaxLength {
    // Main constructor
    constructor(element, config, $root) {
        // New maxlength object
        this.$popper = null;
        this.$element = element;
        this.$id = `__MaxLength_${NEXTID++}__`;
        this.$root = $root || null;
        this.$label = null;
        this.$visibleInterval = null;
        this.$activeTrigger = {};
        this.$state = '';
        // We use a bound version of the following handlers for root/modal listeners to maintain the 'this' context
        this.$doHide = this.doHide.bind(this);
        // Set the configuration
        this.updateConfig(config);
    }

    // Update config
    updateConfig(config) {
        let updatedConfig = {...DEFAULT_CONFIG, ...config};

        // Sanitize delay
        if (!config.maxLength) {
            updatedConfig.maxLength = Number(this.$element.getAttribute('maxlength'));
        }

        // Update the config
        this.$config = updatedConfig;

        // Stop/Restart listening
        this.unListen();
        this.listen();
    }

    // Destroy this instance
    destroy() {
        // Stop listening to trigger events
        this.unListen();
        // Disable while open listeners/watchers
        this.setWhileOpenListeners(false);
        // Remove popper
        if (this.$popper) {
            this.$popper.destroy();
        }
        this.$popper = null;
        // Remove label from document
        if (this.$label && this.$label.parentElement) {
            this.$label.parentElement.removeChild(this.$label);
        }
        this.$label = null;
        // Null out other properties
        this.$id = null;
        this.$root = null;
        this.$element = null;
        this.$config = null;
        this.$state = null;
        this.$activeTrigger = null;
        this.$doHide = null;
    }

    isVisible($element) {
        return $element &&
            document.body.contains($element) &&
            $element.getBoundingClientRect().height > 0 &&
            $element.getBoundingClientRect().width > 0;
    }

    // Show label
    show() {
        if (!document.body.contains(this.$element) || !this.isVisible(this.$element)) {
            // If trigger element isn't in the DOM or is not visible
            return;
        }
        // Build label element (also sets this.$tip)
        const label = this.getLabelElement();
        this.setContent(label);
        label.setAttribute('id', this.$id);

        // Insert label if needed
        if (!document.body.contains(label)) {
            document.body.appendChild(label)
        }

        // Refresh popper
        this.removePopper();
        this.$popper = new Popper(this.$element, label, this.getPopperConfig());

        // Enable while open listeners/watchers
        this.setWhileOpenListeners(true);

        const prevState = this.$state;
        this.$state = null;
        if (prevState === STATE.OUT) {
            this.leave(null)
        }
    }

    // handler for periodic visibility check
    visibleCheck(on) {
        clearInterval(this.$visibleInterval);
        this.$visibleInterval = null;
        if (on) {
            this.$visibleInterval = setInterval(() => {
                const label = this.getLabelElement();
                if (label && !this.isVisible(this.$element)) {
                    // Element is no longer visible, so force-hide the label
                    this.doHide()
                }
            }, 100)
        }
    }

    setWhileOpenListeners(on) {
        // Modal close events
        this.setModalListener(on);
        // Periodic $element visibility check
        // For handling when tip is in <keepalive>, tabs, carousel, etc
        this.visibleCheck(on);
        // Ontouch start listeners
        this.setOnTouchStartListener(on);
        if (!this.$label) {
            return;
        }

        if (on) {
            // If focus moves between trigger element and tip container, dont close
            this.$label.addEventListener('focusout', this);
        } else {
            this.$label.removeEventListener('focusout', this);
        }
    }

    // force hide of tip (internal method)
    doHide() {
        if (!this.$label || !this.isVisible(this.$label)) {
            return;
        }

        // Disable while open listeners/watchers
        this.setWhileOpenListeners(false);
        this.$state = '';
        // Hide the label
        this.hide(null);
    }

    // Hide label
    hide(callback) {
        if (!this.$label) {
            return;
        }

        // Disable while open listeners/watchers
        this.setWhileOpenListeners(false);

        // Hide tip
        this.$label.style.display = 'none';
        this.$activeTrigger.focus = false;

        if (this.$state !== STATE.SHOW && this.$label.parentNode) {
            // Remove tip from dom, and force recompile on next show
            this.$label.parentNode.removeChild(this.$label);
            this.removePopper();
            this.$label = null;
        }

        if (callback) {
            callback()
        }

        this.$state = ''
    }

    removePopper() {
        if (this.$popper) {
            this.$popper.destroy();
        }

        this.$popper = null;
    }

    getLabelElement() {
        if (!this.$label) {
            this.$label = this.compileTemplate(this.$config.template);
        }
        return this.$label;
    }

    compileTemplate(html) {
        if (!html || typeof html !== 'string') {
            return null;
        }
        let div = document.createElement('div');
        div.innerHTML = html.trim();
        const node = div.firstElementChild ? div.removeChild(div.firstElementChild) : null;
        div = null;
        return node;
    }

    getClass() {
        const valueLength = this.$element.value.length || 0;
        const charsLeft = this.$config.maxLength - valueLength;
        let labelClass = 'badge ';
        labelClass += charsLeft <= 0 ? 'badge-danger' : 'badge-success';

        return labelClass;
    }

    // NOTE: Overridden by PopOver class
    setContent(label) {
        const valueLength = this.$element.value.length || 0;
        label.innerHTML = valueLength + '/' + this.$config.maxLength;
        label.className = this.getClass();
    }

    listen() {
        this.$element.addEventListener('focusin', this);
        this.$element.addEventListener('focusout', this);
        this.$element.addEventListener('input', this);
    }

    unListen() {
        if (!this.$element) {
            return;
        }

        this.$element.removeEventListener('focusin', this);
        this.$element.removeEventListener('focusout', this);
        this.$element.removeEventListener('input', this);
    }

    updateContent() {
        if (!this.isVisible(this.getLabelElement())) {
            return;
        }

        this.setContent(this.getLabelElement());
    }

    handleEvent(e) {
        if (e.type === 'input') {
            this.updateContent();
        } else if (e.type === 'focusin') {
            this.enter(e);
        } else if (e.type === 'focusout') {
            // target is the element which is loosing focus
            // And relatedTarget is the element gaining focus
            if (this.$label && this.$element && this.$element.contains(e.target) && this.$label.contains(e.relatedTarget)) {
                // If focus moves from this.$element to this.$label, don't trigger a leave
                return;
            }
            if (this.$label && this.$element && this.$label.contains(e.target) && this.$element.contains(e.relatedTarget)) {
                // If focus moves from this.$label to this.$element, don't trigger a leave
                return;
            }
            if (this.$label && this.$label.contains(e.target) && this.$label.contains(e.relatedTarget)) {
                // If focus moves within this.$label, don't trigger a leave
                return;
            }
            if (this.$element && this.$element.contains(e.target) && this.$element.contains(e.relatedTarget)) {
                // If focus moves within this.$element, don't trigger a leave
                return;
            }

            // Otherwise trigger a leave
            this.leave(e);
        }
    }

    static matchesSelector(el, selector) {
        if (!el || el.nodeType !== Node.ELEMENT_NODE) {
            return false;
        }

        // https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
        // Prefer native implementations over polyfill function
        const proto = Element.prototype;
        const Matches = proto.matches ||
            proto.matchesSelector ||
            proto.mozMatchesSelector ||
            proto.msMatchesSelector ||
            proto.oMatchesSelector ||
            proto.webkitMatchesSelector ||
            function (sel) {
                const element = this;
                const m = Object.values((element.document || element.ownerDocument || document).querySelectorAll(sel));
                let i = m.length;
                while (--i >= 0 && m.item(i) !== element) {
                }
                ;
                return i > -1;
            };

        return Matches.call(el, selector);
    }

    // Finds closest element matching selector. Returns null if not found
    static closestElement(selector, root) {
        if (!root || root.nodeType !== Node.ELEMENT_NODE) {
            return null
        }

        // https://developer.mozilla.org/en-US/docs/Web/API/Element/closest
        // Since we dont support IE < 10, we can use the "Matches" version of the polyfill for speed
        // Prefer native implementation over polyfill function
        const Closest = Element.prototype.closest ||
            function (sel) {
                let element = this;
                if (!document.documentElement.contains(element)) {
                    return null
                }
                do {
                    // Use our "patched" matches function
                    if (MaxLength.matchesSelector(element, sel)) {
                        return element;
                    }
                    element = element.parentElement
                } while (element !== null);
                return null;
            };

        const el = Closest.call(root, selector);
        // Emulate jQuery closest and return null if match is the passed in element (root)
        return el === root ? null : el;
    }

    setModalListener(on) {
        const modal = MaxLength.closestElement('.modal-content', this.$element);
        if (!modal) {
            // If we are not in a modal, don't worry. be happy
            return;
        }
        // We can listen for modal hidden events on $root
        if (this.$root) {
            this.$root[on ? '$on' : '$off']('hidden.bs.modal', this.$doHide);
        }
    }

    setOnTouchStartListener(on) {
        // if this is a touch-enabled device we add extra
        // empty mouseover listeners to the body's immediate children;
        // only needed because of broken event delegation on iOS
        // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
        if ('ontouchstart' in document.documentElement) {
            Object.values(document.body.children).forEach(el => {
                if (!el) {
                    return;
                }

                if (on) {
                    el.addEventListener('mouseover', this._noop);
                } else {
                    el.removeEventListener('mouseover', this._noop);
                }
            })
        }
    }

    _noop() {
        // Empty noop handler for ontouchstart devices
    }

    // Enter handler
    enter(e) {
        if (e) {
            this.$activeTrigger.focus = true;
        }
        if (this.isVisible(this.getLabelElement()) || this.$state === STATE.SHOW) {
            this.$state = STATE.SHOW;
            return;
        }
        this.$state = STATE.SHOW;
        this.show();
    }

    // Leave handler
    leave(e) {
        if (e) {
            this.$activeTrigger.focus = false;
        }
        if (this.isWithActiveTrigger()) {
            return;
        }
        this.$state = STATE.OUT;
        this.hide();
    }

    getPopperConfig() {
        return {
            placement: 'bottom',
            modifiers: {
                offset: {offset: 0}
            },
            onCreate: data => {
                // Handle flipping arrow classes
                if (data.originalPlacement !== data.placement) {
                    this.handlePopperPlacementChange()
                }
            },
            onUpdate: data => {
                // Handle flipping arrow classes
                this.handlePopperPlacementChange()
            }
        }
    }

    isWithActiveTrigger() {
        for (const trigger in this.$activeTrigger) {
            if (this.$activeTrigger[trigger]) {
                return true;
            }
        }
        return false;
    }

    // NOTE: Overridden by PopOver class
    cleanTipClass() {
        const label = this.getLabelElement();
        const tabClass = label.className.match(new RegExp(`\\bpl-maxlength\\S+`, 'g'));
        if (tabClass !== null && tabClass.length > 0) {
            tabClass.forEach(cls => {
                if (cls) {
                    label.classList.remove(cls);
                }
            })
        }
    }

    handlePopperPlacementChange() {
        this.cleanTipClass();
    }
}
