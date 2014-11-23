/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 */

$(document).ready(function () {
    translate.init();
});

var translate = (function () {

    /**
     * @type string
     */
    var _originalMessage;

    /**
     * @param object $this
     */
    function _translateLanguage($this) {
        var data = {
            id: $this.data('id'),
            language_id: $('#language_id').val(),
            translation: $.trim($this.closest('tr').find('.translation').val())
        };

        helpers.post('save', data);
    }

    /**
     * @param object $this
     */
    function _copySourceToTranslation($this) {
        if ($.trim($this.closest('tr').find('.translation').val()).length === 0) {
            $this.closest('tr').find('.translation').val($.trim($this.val()));
        }

        _translateLanguage($this.closest('tr').find('button'));
    }

    return {
        init: function () {
            $('#translates').on('click', '.source', function () {
                _copySourceToTranslation($(this));
            });
            $('#translates').on('click', 'button', function () {
                _translateLanguage($(this));
            });
            $('#translates').on('focus', '.translation', function () {
                _originalMessage = $.trim($(this).val());
            });
            $('#translates').on('blur', '.translation', function () {
                if ($.trim($(this).val()) !== _originalMessage) {
                    _translateLanguage($(this).closest('tr').find('button'));
                }
            });
        }
    }
})();