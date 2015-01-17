/** 
 * Created on : 2014.12.04., 16:58:40
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 */

$(document).ready(function () {
    FrontendTranslation.init();
});

/**
 * Object facilitating front end translation
 */
var FrontendTranslation = {
    enabledTranslate: false,
    dialogURL: '/translatemanager/language/dialog',
    saveURL: '/translatemanager/language/save',
    messageURL: '/translatemanager/language/message',
    params: '',
    dialog: function ($language_item) {
        this.params = $language_item.data('params');
        $('#translate-manager-div').dialog({
            modal: true,
            title: lajax.t('Translation Language: {name}', {name: $language_item.data('language_id')}),
            minWidth: 500,
            minHeight: 200,
            buttons: [
                {
                    text: lajax.t('Save'),
                    click: $.proxy(
                            function () {
                                var translation = $('#translate-manager-translation').val();
                                $.post(this.saveURL, {
                                    id: $('#translate-manager-id').val(),
                                    language_id: $('#translate-manager-language_id').val(),
                                    translation: translation
                                }, $.proxy(
                                        function (errors) {
                                            if (errors.length === 0) {
                                                $('span[data-hash=' + $language_item.data('hash') + ']').html(lajax.t(translation, this.params));
                                            }
                                        }, this));
                                $('#translate-manager-div').dialog('close');
                            }, this)

                },
                {
                    text: lajax.t('Close'),
                    click: function () {
                        $(this).dialog('close')
                    }
                }
            ],
            create: $.proxy(
                    function (event) {
                        $(event.target).load(this.dialogURL, {
                            hash: $language_item.data('hash'),
                            category: $language_item.data('category'),
                            language_id: $language_item.data('language_id')
                        })
                    }, this),
            close: function () {
                $('#translate-manager-div').dialog('destroy').html('');
            }
        });
    },
    changeSourceLanguage: function (languageId) {
        $('#translate-manager-message').load(this.messageURL, {
            id: $('#translate-manager-id').val(),
            language_id: languageId
        });
    },
    addClick: function() {
        $('span.language-item.translatable').click($.proxy(function (event) {
            if (this.enabledTranslate) {
                this.dialog($(event.currentTarget));
                event.stopPropagation();
                return false;
            }
        }, this));
    },
    toggleTranslate: function () {
        var elements = $('.language-item');
        elements.toggleClass('translatable');
        this.enabledTranslate = elements.hasClass('translatable');
        this.addClick();
    },
    init: function () {
        $('body').on('change', '#translate-manager-language-source', $.proxy(function (event) {
            this.changeSourceLanguage($(event.currentTarget).val());
        },this));
        $('body').on('click', '#toggle-translate', $.proxy(function () {
            this.toggleTranslate();
        }, this));
    }
}


