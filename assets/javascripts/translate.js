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
        var $translation = $this.closest('tr').find('.translation');

        var data = {
            id: $translation.data('id'),
            language_id: $('#language_id').val(),
            translation: $.trim($translation.val())
        };

        helpers.post($('#language_id').data('url'), data);
    }

    /**
     * @param {object} $this
     */
    function _copySourceToTranslation($this) {
        var $translation = $this.closest('tr').find('.translation'),
            isEmptyTranslation = $.trim($translation.val()).length === 0,
            sourceMessage = $.trim($this.val());

        if (!isEmptyTranslation) {
            return;
        }

        $translation.val(sourceMessage);
        _translateLanguage($this);
    }

    /**
     * @param {$} $btn
     */
    function _translateText($btn) {
        var $translation = $btn.closest('tr').find('.translation');
        console.log($translation);

        helpers.post('/translatemanager/language/translate-text', {
            id: $translation.data('id'),
            language_id: $('#language_id').val()
        });
    }

    return {
        init: function () {
            $('#translates').on('click', '.source', function () {
                _copySourceToTranslation($(this));
            });
            $('#translates').on('click', '.js-translate-text', function () {
                _translateText($(this));
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
