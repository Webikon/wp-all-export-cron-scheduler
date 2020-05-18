
jQuery(function ($) {
    var exportsList = $('#wpae-crsch-exports-list');

    // Add export item
    $(document).on('click', '.js-wpae-crsch-add-item', function (e) {
        e.preventDefault();

        $('.wpae-crsch-export-item:last').clone().appendTo(exportsList).find('select > option').removeAttr('selected');
    });

    // Remove export item
    $(document).on('click', '.js-wpae-crsch-remove-item', function (e) {
        e.preventDefault();

        var cloneItem = $('.wpae-crsch-export-item');

        $(this).closest(cloneItem).remove();
    });
});