function initAdminKit(item_selector) {
    $('#form_buttons').prepend(
        $('<button class="btn btn-secondary mr-2" type="button">Clear seleciton</button>')
            .click(function(){
                $('form input:checkbox')
                    .prop("checked", false)
                    .parents(item_selector).removeClass('checked');
            }),
        $('<button class="btn btn-secondary mr-2" type="button">Select all</button>')
            .click(function(){
                $('form input:checkbox')
                    .prop("checked", true)
                    .parents(item_selector).addClass('checked');;
            })
    );

    $('form').on("click", item_selector, function(){
        var checkbox = $(this).find("input:checkbox");
        var value = checkbox.prop("checked");
        
        checkbox.prop("checked", !value);
        $(this).toggleClass('checked', !value);
    });
}
