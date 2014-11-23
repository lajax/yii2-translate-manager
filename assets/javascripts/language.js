/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 */

$(document).ready(function() {
    language.init();
});

var language = (function() {

    /**
     * Change language status.
     * @param object $this
     */
    function _changeStatus($this) {
        var data = {
            language_id: $this.attr('id'),
            status: $this.val()
        };

        helpers.post('change-status', data);
    }

    return {
        init: function() {
            $('#languages').on('change', 'select.status', function() {
                _changeStatus($(this));
            });
        }
    }
})();