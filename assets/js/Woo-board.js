jQuery(document).on('click', '#defaultChecked', function() {
    if(jQuery(this).prop("checked") == false) {
        jQuery('.SelectedId').prop("checked", false);
    } else {
        jQuery('.SelectedId').prop("checked", true);
    }
});

jQuery(document).on('click', '#addThisAttr', function() {
    let attr = jQuery(this).data('attr');
    jQuery.ajax({
        url: ajaxurl,
        type: "post",
        data: {action: 'WSPO_attribute_creation', 'name':attr},
        success:function(results) {
            if(results === false)
                window.location.href = window.location.pathname + window.location.search + "&creat_attr=false";
            else
                window.location.href = window.location.pathname + window.location.search + "&creat_attr=true";
        }
    });
});