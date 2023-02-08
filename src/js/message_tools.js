function installMessageTools(){
    var insertText = function(beginText, endText, removeSelected) {
        var textarea = $("#form_container textarea").get(0);
        var value = textarea.value;
        var selStart = textarea.selectionStart;
        var selEnd = textarea.selectionEnd;
        textarea.value = value.substring(0, selStart) + beginText + (removeSelected ? "" : value.substring(selStart, selEnd))
            + endText + value.substring(selEnd, value.length);
        textarea.selectionStart = selStart + beginText.length;
        textarea.selectionEnd = (removeSelected ? selStart : selEnd) + beginText.length;
        textarea.focus();
    };
    
    var textTemplates = {
        "p": ["[p]", "[/p]"],
        "b": ["[b]", "[/b]"],
        "i": ["[i]", "[/i]"],
        "u": ["[u]", "[/u]"],
        "center": ["[center]", "[/center]"],
        "right": ["[right]", "[/right]"],
        "br": ["[br]", ""],
        "link": ["[link][/url]", "[/link]"],
        "color": ["[color][/rgb]", "[/color]"],
        "bgcolor": ["[bgcolor][/rgb]", "[/bgcolor]"]
    };
    
    $('#emoticons')
        .show()
        .find('img').each(function(i, element){
            $(element).click(function(){
                insertText(element.alt, "", true);
            });
        });
      
    $("#form_options")
        .show()
        .find('img').each(function(i, element){
            $(element).click(function(){
                var template = textTemplates[element.alt];
                insertText(template[0], template[1], false);
            });
        });
}
