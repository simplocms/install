/**
 * Basic utilities.
 * @abstract
 */
window.Utils = class Utils {
    /**
     * Generate random 4-char string.
     * @static
     * @return {string}
     */
    static S4() {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    }

    /**
     * Generate Global unique string.
     * @static
     * @returns {string}
     */
    static guid() {
        return (
            Utils.S4() + Utils.S4() + '-' + Utils.S4() + '-4' + Utils.S4().substr(0, 3) + '-' +
            Utils.S4() + '-' + Utils.S4() + Utils.S4() + Utils.S4()
        ).toLowerCase();
    }
};
