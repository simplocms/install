window.Converter = class Converter {

    /**
     * Remove diacritics from the text.
     * 
     * @param {string} text 
     * @param {string} divider 
     */
    static removeDiacritics (text, divider = '-') {
        var map = window.Converter.diacriticsMap;
        var text = text.split('');

	    for (var i = 0; i < text.length; i++) {
            var replacement = map[text[i]];

	        if (text[i] === '' || typeof replacement === 'undefined') {
	            continue;
            }

            text[i] = (text[i] + '').split(text[i]).join(replacement);
        }
        
        return text.toString()
                   .toLowerCase() // change everything to lowercase
                   .replace(/^\s+|\s+$/g, "") // trim leading and trailing spaces		
                   .replace(/[_|\s]+/g, "-") // change all spaces and underscores to a hyphen
                   .replace(/[^a-z\u0400-\u04FF0-9-]+/g, "") // remove all non-cyrillic, non-numeric characters except the hyphen
                   .replace(/[-]+/g, "-") // replace multiple instances of the hyphen with a single instance
                   .replace(/^-+|-+$/g, "") // trim leading and trailing hyphens
                   .replace(/[-]+/g, divider)	
	}
};

window.Converter.diacriticsMap = {
    "б": "b", "в": "v", "г": "g", "д": "d", "ж": "zh", "з": "z", "и": "i", "й": "y", "к": "k", "л": "l", "м": "m", "н": "n", 
    "о": "o", "п": "p", "р": "r", "с": "s", "т": "t", "у": "u", "ф": "f", "х": "h", "ц": "ts", "ч": "ch", "ш": "sh", "щ": "sht", 
    "ъ": "a", "ь": "y", "ю": "yu", "я": "ya", "А": "A", "Б": "B", "В": "B", "Г": "G", "Д": "D", "Е": "E", "Ж": "Zh", "З": "Z", 
    "И": "I", "Й": "Y", "К": "K", "Л": "L", "М": "M", "Н": "N", "О": "O", "П": "P", "Р": "R", "С": "S", "Т": "T", "У": "U", 
    "Ф": "F", "Х": "H", "Ц": "Ts", "Ч": "Ch", "Ш": "Sh", "Щ": "Sht", "Ъ": "A", "Ь": "Y", "Ю": "Yu", "Я": "Ya",

    "Ї": "I", "ї": "i", "Є": "Ye", "є": "ye", "Ы": "I", "ы": "i", "Ё": "Yo", "ё": "yo",
    "ı": "i", "İ": "I", "ğ": "g", "Ğ": "G", "ü": "u", "Ü": "U", "ş": "s", "Ş": "S", "ö": "o", "Ö": "O", "ç": "c", "Ç": "C",
    "Á": "A", "á": "a", "Â": "A", "â": "a", "Ã": "A", "ã": "a", "À": "A", "à": "a", "Ç": "C", "ç": "c", "É": "E", "é": "e", 
    "Ê": "E", "ê": "e", "Í": "I", "í": "i", "Ó": "O", "ó": "o", "Ô": "O", "ô": "o", "Õ": "O", "õ": "o", "Ú": "U", "ú": "u", 
    "Ñ": "N", "ñ": "n", "È": "E", "è": "e", "ě": "e", "Ě": "E", "š": "s", "Š": "S", "č": "c", "Č": "C", "ř": "r", "Ř": "R",
    "ž": "z", "Ž": "Ž", "ý": "y", "Ý": "Y", "ď": "d", "Ď": "D", "ľ": "l", "Ľ": "L", "ť": "t", "Ť": "T", "ů": "u", "Ů": "U",
    "ň": "n", "Ň": "N", "Ä": "A", "ä": "a", "Ĺ": "L", "ĺ": "l", "ő": "o", "Ő": "O", "ŕ": "r", "Ŕ": "R", "ű": "u", "Ű": "U"
};
