/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 */

var helpers = (function() {

    /**
     * @type Boolean
     */
    var _load = false;

    /**
     * Remove alert box.
     */
    function _hideMessages() {
        setTimeout(function() {
            $('#alert-box').remove();
        }, 5000);
    }

    /**
     * Remove alert tooltip.
     */
    function _hideTooltip() {
        setTimeout(function() {
            $('#alert-tooltip').remove();
        }, 500);
    }

    return {
        /**
         * @param string url
         * @param json data
         */
        post: function(url, data) {
            if (_load === false) {
                _load = true;
                $.post(url, data, $.proxy(function(data) {
                    _load = false;
                    this.showTooltip(data);
                }, this), 'json');
            }
        },
        /**
         * Show alert tooltip.
         * @param json data
         */
        showTooltip: function(data) {

            if ($('#alert-tooltip').length === 0) {
                var $alert = $('<div>')
                        .attr({id: 'alert-tooltip'})
                        .addClass(data.length < 3 ? 'green' : 'red')
                        .append($('<span>')
                                .addClass('glyphicon')
                                .addClass(data.length < 3 ? ' glyphicon-ok' : 'glyphicon-remove'));

                $('body').append($alert);
                _hideTooltip();
            }
        },
        /**
         * Show messages.
         * @param json data
         */
        showMessages: function(data) {

            if ($('#alert-box').length) {
                $('#alert-box').append(data.messages);
            } else {
                var $messages = $('<div>')
                        .attr({id: 'alert-box', role: 'alert'})
                        .addClass('alert')
                        .addClass(typeof (data.class) === 'undefined' ? 'alert-info' : data.class)
                        .text(data.messages);

                $($('body').find('.container').eq(1)).prepend($messages);
                _hideMessages();
            }
        }
    }
})();