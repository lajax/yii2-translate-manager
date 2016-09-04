/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 * since 1.0
 */

var translate = (function () {

    /**
     * @type string
     */
    var _originalMessage;

    /**
     * @param {object} $this
     */
    function _translateLanguage($this) {
        var data = {
            id: $this.data('id'),
            language_id: $('#language_id').val(),
            translation: $.trim($this.closest('tr').find('.translation').val())
        };

        helpers.post($('#language_id').data('url'), data);
    }

    /**
     * @param {object} $this
     */
    function _copySourceToTranslation($this) {

        if(typeof x_googleApiKey == 'undefined') // default bahavior - copy original text to translation field
        {
            if ($.trim($this.closest('tr').find('.translation').val()).length === 0) {
                $this.closest('tr').find('.translation').val($.trim($this.val()));
            }

            _translateLanguage($this.closest('tr').find('button'));
        }
        else  // google translation is enabled - translate and copy translation ...
        {
            if ($.trim($this.closest('tr').find('.translation').val()).length === 0) {
                helpers.googleTranslate($.trim($this.val()), $('#language_id').val(), function(result) {
                    $this.closest('tr').find('.translation').val(result);
                    _translateLanguage($this.closest('tr').find('button'));
                });
            }
        }
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
            $('#translates').on('change', "#search-form select", function(){
                $(this).parents("form").submit();
            });
        }
    };
})();

$(document).ready(function () {
    translate.init();
});
