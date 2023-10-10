jQuery(document).ready(function($) {
    $('#side-sortables').append('<div id="woocommerce-order-actions" class="postbox "><div class="postbox-header"><h2 class="hndle ui-sortable-handle">Gerar pdf do orçamento</h2><div class="handle-actions hide-if-no-js"><button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="woocommerce-order-actions-handle-order-higher-description"><span class="screen-reader-text">Mover para cima</span><span class="order-higher-indicator" aria-hidden="true"></span></button><span class="hidden" id="woocommerce-order-actions-handle-order-higher-description">Mover a caixa Ações do Pedido para cima</span><button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="woocommerce-order-actions-handle-order-lower-description"><span class="screen-reader-text">Mover para baixo</span><span class="order-lower-indicator" aria-hidden="true"></span></button><span class="hidden" id="woocommerce-order-actions-handle-order-lower-description">Mover a caixa Ações do Pedido para baixo</span><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Alternar painel: Ações do Pedido</span><span class="toggle-indicator" aria-hidden="true"></span></button></div></div><div class="inside"><ul class="order_actions submitbox"><li><button type="submit" class="button generate_pdf button-primary" name="generate_pdf">Gerar pdf</button></li></ul></div></div>')

    $(document).on('click' , '.generate_pdf' , function(e){
        e.preventDefault()
        var orderText = $('.woocommerce-order-data__heading').text()
        var numbers = orderText.match(/\d+/);
        var orderID = numbers[0]
        $.ajax({
            url: wpurl.ajax,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'generate_pdf_ajax',
                order_id: orderID
            },
            success: function(response) {
                var pdf_url = response.data.pdf_url
                if (response && pdf_url) {
                    var downloadLink = document.createElement('a');
                    downloadLink.href = pdf_url;
                    downloadLink.download = 'Orcamento-'+response.data.pdf_date+'.pdf';
                    downloadLink.click();
                } else {
                    console.error('A resposta da solicitação AJAX não contém uma URL de PDF válida.');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
        
    })

});