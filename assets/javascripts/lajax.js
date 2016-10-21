/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 * since 1.0
 */

/**
 * This module facilitates client-side multilingual support.
 */
var lajax = (function () {

    return {
        /**
         * 
         * Method that facilitates multilingual support on client side.
         * 
         * examples:
         * 
         * ~~~
         * alert(lajax.t('Hello!'));
         * alert(lajax.t('Hello {name}'), {name:'World!'});
         * alert(lajax.t('Hello {first_name} {last_name}', {fist_name: 'Veronica', last_name:'Hunter'}));
         * ~~~
         * 
         * @param {string} message The message to translate.
         * @param {object} $params Parameter to change within text as json string, i.e.: {fist_name: 'Veronica', last_name:'Hunter'}
         * @returns {string}
         */
        t: function (message, $params) {
            if (
                    typeof (languageItems) !== 'undefined' &&
                    typeof (language) !== 'undefined' &&
                    typeof (languageItems[language]) !== 'undefined' &&
                    typeof (languageItems[language].getLanguageItems) === 'function') {
                var $messages = languageItems[language].getLanguageItems();
                if (typeof ($messages) !== 'undefined') {
                    var hash = md5(message);
                    if (typeof ($messages[hash]) !== 'undefined') {
                        message = $messages[hash];
                    }
                }
            }

            if (typeof ($params) !== 'undefined') {
                for (search in $params) {
                    message = message.replace('{' + search + '}', $params[search]);
                }
            }

            return message;
        }
    };
})();

