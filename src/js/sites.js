function divideIntoSites(selector, item_selector='div.record', site_size=10) {
    var container = $(selector);
    var records = container.find('> ' + item_selector);
    
    var sites_count = Math.ceil(records.length / site_size);
    
    if (sites_count <= 1)
        return; // do not divide if all fits on single site
    
    var ul = $('<ul class="pagination justify-content-center mt-2"></ul>');
    
    var show_site = function(i) {
        container
            .find('div.records-group').hide()
            .eq(i).show();

        ul
            .find('li').removeClass('active')
            .eq(i).addClass('active');
    };

    for (var i = 0; i < sites_count; i++) {
        $('<div class="records-group" />')
            .hide()
            .append(records.slice(i * site_size, (i+1) * site_size))
            .appendTo(container);

        $('<li class="page-item" />')
            .click(function() { show_site($(this).index()); return false;})
            .addClass()
            .append($('<a class="page-link" href="#"></a>').text(i+1))
            .appendTo(ul);
    }

    $('<li class="page-item" />')
        .click(function(){
            container.find('div.records-group').show();
            ul.find('li').removeClass('active');
            $(this).addClass('active');
            return false;
        })
        .append($('<a class="page-link" href="#"></a>').text('Show all'))
        .appendTo(ul);

    $('<nav aria-label="Sites pagination"></nav>')
        .append(ul)
        .appendTo(container);
    
    show_site(0);
}
