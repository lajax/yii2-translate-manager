/**
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 * since 1.0
 */

var helpers = (function () {

    /**
     * @type Boolean
     */
    var _load = false;

    /**
     * Remove alert box.
     */
    function _hideMessages() {
        setTimeout(function () {
            $('.alert-box').remove();
        }, 5000);
    }

    /**
     * Remove alert tooltip.
     */
    function _hideTooltip() {
        setTimeout(function () {
            $('#alert-tooltip').remove();
        }, 500);
    }

    function _createMessage(message, type) {
        return $('<div>')
                .attr({'class': 'alert-box', role: 'alert'})
                .addClass('alert')
                .addClass(typeof (type) === 'undefined' ? 'alert-info' : type)
                .text(message);
    }

    return {
        /**
         * @param {string} url
         * @param {json} $data
         */
        post: function (url, $data) {
            if (_load === false) {
                _load = true;
                $.post(url, $data, $.proxy(function (data) {
                    _load = false;
                    this.showTooltip(data);
                }, this), 'json');
            }
        },
        /**
         * Show alert tooltip.
         * @param {json} $data
         */
        showTooltip: function ($data) {

            if ($('#alert-tooltip').length === 0) {
                var $alert = $('<div>')
                        .attr({id: 'alert-tooltip'})
                        .addClass($data.length === 0 ? 'green' : 'red')
                        .append($('<span>')
                                .addClass('glyphicon')
                                .addClass($data.length === 0 ? ' glyphicon-ok' : 'glyphicon-remove'));

                $('body').append($alert);
                _hideTooltip();
            }
        },
        /**
         * Show messages.
         * @param {json} $data
         * @param {string} container
         */
        showMessages: function ($data, container) {

            if ($('.alert-box').length) {
                $('.alert-box').append($data);
            } else {
                $(typeof (container) === 'undefined' ? $('body').find('.container').eq(1) : container).prepend(_createMessage($data));
                _hideMessages();
            }
        },
        /**
         * Show error messages.
         * @param {json} $data
         * @param {string} prefix
         */
        showErrorMessages: function ($data, prefix) {
            for (i in $data) {
                var k = 0;
                $messages = new Array();
                if (typeof ($data[i]) === 'object') {
                    for (j in $data[i]) {
                        $messages[k++] = $data[i][j];
                    }
                } else {
                    $messages[k++] = $data[i];
                }

                this.showErrorMessage($messages.join(' '), prefix + i);
            }
            _hideMessages();
        },
        /**
         * Show error message.
         * @param {string} message
         * @param {string} id
         */
        showErrorMessage: function (message, id) {
            $(id).next().html(_createMessage(message, 'alert-danger'));
        }
    };
})();
