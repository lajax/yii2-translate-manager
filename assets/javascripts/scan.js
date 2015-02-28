/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 * since 1.4
 */

$(document).ready(function () {
    scan.init();
});

/**
 * Object for removing non-used language elements.
 * @type {object}
 */
var scan = {
    object: null,
    checked: false,
    init: function () {
        $('body').on('click', 'button#select-all', $.proxy(function () {
            this.toggleChecked();
        }, this));
        $('body').on('click', 'button#delete-selected', $.proxy(function () {
            if (window.confirm(lajax.t('Are you sure you want to delete these items?'))) {
                this.deleteSelected();
            }
        }, this));
        $('body').on('click', 'a.delete-item', $.proxy(function (event) {
            if (window.confirm(lajax.t('Are you sure you want to delete this item?'))) {
                this.deleteItem($(event.currentTarget));
            }
            return false;
        }, this));
    },
    toggleChecked: function () {
        this.checked = !this.checked;
        $('#delete-source').find('input.language-source-cb').prop("checked", this.checked);
    },
    deleteSelected: function () {
        var $ids = new Array();

        this.object = $('#delete-source').find('input.language-source-cb:checked');
        this.object.each(function () {
            $ids.push($(this).val());
        });

        this.delete($ids);
    },
    deleteItem: function ($object) {
        this.object = $object;

        var $ids = new Array();
        $ids.push(this.object.data('id'));

        this.delete($ids);
    },
    delete: function ($ids) {
        if ($ids.length) {
            $.post($('#delete-source').find('a').attr('href'), {ids: $ids}, $.proxy(function () {
                this.remove();
            }, this));
        }
    },
    remove: function () {
        this.object.closest('tr').remove();

        var text = $('#w2-danger').text();
        var pattern = /\d+/g;
        var number = pattern.exec(text);
        $('#w2-danger').text(text.replace(number, $('#delete-source').find('tbody').find('tr').length));

        if ($('#delete-source').find('tbody').find('tr').length === 0) {
            $('#delete-source, #select-all, #delete-selected').remove();
        }
    }
};

