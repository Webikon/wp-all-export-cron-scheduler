
jQuery(function ($) {
    var exportsList = $('#wpae-crsch-exports-list');

    // Add export item
    $(document).on('click', '.js-wpae-crsch-add-item', function (e) {
        e.preventDefault();

        var itemsCount = exportsList.children().length;

        $('.wpae-crsch-export-item:last')
            .clone()
            .appendTo(exportsList)
            .find('.wpae-crsch-input, .wpae-crsch-input > option')
                .removeAttr('selected')
                .removeAttr('checked')
                .attr('name', function (index, currentvalue) {
                    if (currentvalue) {
                        return currentvalue.replace(/[0-9]/, itemsCount);
                    }
                });
    });

    // Remove export item
    $(document).on('click', '.js-wpae-crsch-remove-item', function (e) {
        e.preventDefault();

        var cloneItem = $('.wpae-crsch-export-item');

        $(this).closest(cloneItem).remove();
    });
});