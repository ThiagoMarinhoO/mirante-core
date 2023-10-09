<?php
function add_custom_bulk_action_to_order($actions) {
    global $post;
    if ($post->post_type === 'shop_order') {
        $actions['generate_pdf'] = 'Gerar PDF';
    }
    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'add_custom_bulk_action_to_order');

// Lide com a ação em massa quando ela for executada
function handle_custom_bulk_action_to_order($redirect_to, $action, $post_ids) {
    if ($action === 'generate_pdf') {
        // Lógica para a ação "Gerar PDF"
        foreach ($post_ids as $post_id) {
            // Obtenha o pedido individual e realize as ações necessárias para gerar o PDF
            $order = wc_get_order($post_id);
            if ($order) {
                generate_pdf($order);
            }
        }
    }

    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-shop_order', 'handle_custom_bulk_action_to_order', 10, 3);

use FPDF as GlobalFPDF;
function generate_pdf($order) {
    require(plugin_dir_path(__FILE__) . '../vendor/setasign/fpdf/fpdf.php');
    ob_clean();

    $pdf = new GlobalFPDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial', '', 12);

    $logo_path = plugin_dir_url(__FILE__) . '../assets/images/mirantelogo.png';
    $pdf->Image($logo_path, 10, 10, 40);

    $pdf->SetX(70);
    $pdf->MultiCell(0, 10, utf8_decode("Mirante - Móveis Corporativos\nR. Humberto de Campos, 470 A - loja 01 - Jardim Limoeiro, Serra - ES, 29164-034"), 0, 'R');

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(70, 10, utf8_decode('Fatura'), 0, 0); // L para alinhar à esquerda

    // Informações do Pedido à direita
    $pdf->SetFont('Arial', '', 10); // Reduzir tamanho da fonte
    $pdf->Cell(0, 8, utf8_decode('Número do Pedido: ' . $order->get_order_number()), 0, 1, 'R'); // R para alinhar à direita
    $pdf->Cell(0, 8, utf8_decode('Data do Pedido: ' . $order->get_date_created()->format('d/m/Y')), 0, 1, 'R');

    // Informações da Fatura à esquerda
    $pdf->SetFont('Arial', '', 10 , 'L');
    $pdf->Cell(0, 8, utf8_decode($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()), 0, 0, 'L');
    $pdf->Cell(0, 8, utf8_decode($order->get_billing_phone()), 0, 1, 'L');
    $pdf->Cell(0, 8, utf8_decode($order->get_billing_email()), 0, 1, 'L');
    $pdf->Cell(0, 8, utf8_decode($order->get_billing_address_1() . ', ' . $order->get_billing_address_2()), 0, 1, 'L');
    $pdf->Cell(0, 8, utf8_decode($order->get_billing_city()), 0, 1, 'L');
    $pdf->Cell(0, 8, utf8_decode($order->get_billing_country()), 0, 1, 'L');

    // Espaçamento superior
    $pdf->Ln(10);

    // Adicione uma tabela para listar os produtos
    $pdf->SetFillColor(0, 0, 0); // Fundo preto para as labels
    $pdf->SetTextColor(255, 255, 255); // Texto branco para as labels
    $pdf->Cell(30, 10, utf8_decode('Imagem'), 1, 0, 'C', 1);
    $pdf->Cell(70, 10, utf8_decode('Nome do Produto'), 1, 0, 'C', 1);
    $pdf->Cell(10, 10, utf8_decode('Quantidade'), 1, 0, 'C', 1);
    $pdf->Cell(20, 10, utf8_decode('Preço'), 1, 1, 'C', 1);

    $pdf->SetFillColor(255, 255, 255); // Restaura a cor de fundo padrão
    $pdf->SetTextColor(0, 0, 0); // Restaura a cor do texto padrão

    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $product_name = $product->get_name();
        $product_quantity = $item->get_quantity();
        $product_price = $product->get_price();

        // Ajuste o tamanho das células com MultiCell para acomodar o conteúdo
        $pdf->Cell(30, 10, '', 1, 0, 'C'); // Imagem do produto (adicionar a imagem aqui)
        $pdf->MultiCell(60, 10, utf8_decode($product_name), 1, 'L'); // Usar MultiCell para texto longo
        $pdf->Cell(10, 10, utf8_decode($product_quantity), 1, 0, 'C');
        $pdf->Cell(20, 10, utf8_decode($product_price), 1, 1, 'R');
    }

    $filename = 'Orcamento-' . $order->get_date_created()->format('d-m-Y') . '-' . date('d-m-Y') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $pdf->Output('D', $filename);

    die();
}




add_action('wp_ajax_generate_pdf', 'generate_pdf');
add_action('wp_ajax_nopriv_generate_pdf', 'generate_pdf');
?>