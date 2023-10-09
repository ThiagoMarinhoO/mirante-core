jQuery(document).ready(function($) {
    $('#generate-pdf-button').click(function() {
        var order_id = $(this).data('order-id');
        $.ajax({
            type: 'POST',
            url: wpurl.ajax,
            data: {
                action: 'generate_pdf',
                order_id: order_id
            },
            success: function(response) {
                console.log(response)
                window.open('data:application/pdf;base64,' + response);
            }
        });
    });
});