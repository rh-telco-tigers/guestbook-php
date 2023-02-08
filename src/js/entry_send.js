function installAjax() {
    $('#form_container form').submit(function(){
        $.ajax({
            method: "POST",
            url: "save.php?ajax=1",
            processData: "application/x-www-form-urlencoded", 
            data: $(this).serialize()
        })
            .done(function(msg) {
                $("#response").html(msg);
                if($('#response .alert-success').length){
                    $('#form_container').hide();
                    $('#response_buttons').show();
                }
        })
            .fail(function(jqXHR, textStatus) { 
                alert( "Request failed: " + textStatus );
        });
        
        return false;
    });
}
