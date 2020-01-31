window.Localization = class Localization {
    /**
     * Initialize localization class.
     * @param {object} data
     */
    constructor(data) {
        this.data = data;
    }


    /**
     * Get translation under specified key.
     *
     * @param {string} key - use dot notation
     * @param {Object|null} [replacements=null] - associative array: { key: value, ... }
     * @param {*} [defaultValue]
     * @returns {Object|string|*}
     */
    trans(key, replacements = null, defaultValue) {
        let message = this.data[key];

        // Return object (associative array) of multiple translation texts.
        if (typeof message === 'object') {
            return message;
        }

        const splitKey = key.split('.');
        if (splitKey.length > 1) {
            let subset = this.data;

            for (const i in splitKey) {
                subset = subset[splitKey[i]];

                if (typeof subset === 'undefined') {
                    return typeof defaultValue === 'undefined' ? key : defaultValue;
                }
            }

            // Return anything that is not a string.
            if (typeof subset !== 'string') {
                return subset;
            }

            // Here we have string message.
            message = subset;
        }

        if (typeof replacements === 'object') {
            message = Localization.applyReplacements(message, replacements);
        }

        return message;
    }


    /**
     * Chooses one of multiple message versions, based on pluralization rules.
     *
     * @param {string} key - use dot notation
     * @param {number} count - subject count for pluralization
     * @param {Object} [replacements=null] - associative array: { key: value, ... }
     * @param {*} [defaultValue]
     * @return {string|Object|*}
     */
    choice(key, count, replacements = null, defaultValue) {
        const message = this.trans(key, replacements, null);

        // Message not found
        if (message === null) {
            return defaultValue;
        }

        // Message is not a string
        if (typeof message !== 'string') {
            return message;
        }

        const splitMessage = message.split('|');

        // Message does not have multiple variants.
        if (splitMessage.length === 1) {
            return splitMessage[0];
        }

        const messagesWithRules = [];
        const regex = /{\d+}\s(.+)|\[\d+,\d+\]\s(.+)|\[\d+,\*\]\s(.+)/;

        for (let i = 0; i < splitMessage.length; i++) {
            splitMessage[i] = splitMessage[i].trim();

            if (regex.test(splitMessage[i])) {
                const messageSpaceSplit = splitMessage[i].split(/\s/);

                messagesWithRules.push({
                    rule: messageSpaceSplit.shift(),
                    message: messageSpaceSplit.join(' ')
                });
            }
        }

        // Search for rule that matches given count.
        const result = messagesWithRules.find(item => Localization._testPluralizationRule(count, item.rule));

        // When there is no result, it is probably simple form singular|plural OR human error
        if (!result) {
            return splitMessage[count === 1 ? 0 : 1];
        }

        return result.message;
    }


    /**
     * Tests pluralization rule - if value matches interval
     *
     * @param {number} count
     * @param {string} rule
     * @returns {boolean}
     */
    static _testPluralizationRule(count, rule) {
        if (!rule.length) {
            return false;
        }

        switch (rule[0]) {
            // match exact numbers: {1}, {0,1}, {1,2,3,4}, ...
            case '{':
                return rule.substr(1, rule.length - 2).split(',').some(number => {
                    return parseInt(number.trim()) === count;
                });

            // match interval: [1,19], [20,*]
            case '[':
                const interval = rule.substr(1, rule.length - 2).split(',');

                if (interval.length !== 2) {
                    return false;
                }

                const min = parseInt(interval[0].trim());
                const max = interval[1].trim();

                return min <= count && (max === '*' || count <= parseInt(max));
        }

        return false;
    }


    /**
     * Replaces keys in the message by specified values.
     *
     * @param {string} message
     * @param {Object} replacements - associative array: { key: value, ... }
     * @return {string}
     */
    static applyReplacements(message, replacements) {
        for (const key in replacements) {
            const value = replacements[key];
            message = message.replace(new RegExp(':' + key, 'g'), value);
        }

        return message;
    }
};
