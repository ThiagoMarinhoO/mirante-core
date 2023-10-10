<?php
function add_custom_bulk_action_to_order($actions) {
    global $post;
    if ($post->post_type === 'shop_order') {
        $actions['generate_pdf'] = 'Gerar PDF';
    }
    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'add_custom_bulk_action_to_order');


function handle_custom_bulk_action_to_order($redirect_to, $action, $post_ids) {
    if ($action === 'generate_pdf') {
        
        foreach ($post_ids as $post_id) {
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
    $logo_path = plugin_dir_url(__FILE__) . '../assets/images/mirantelogo.png';

    $pdf = new FPDF('L','mm','A4');

    $pdf->AddPage();

    $pdf->SetFont('Arial','',12);
    
    $fontSize=12;

    $pdf->Cell(114, 6, $pdf->Image($logo_path, 10, 10, 60), 0, 0);
    $pdf->SetFont('Arial', "B", 10);
    $pdf->Cell(0, 6, utf8_decode("Mirante - Móveis Corporativos"), 0, 1, "R"); 

    $pdf->Cell(114, 6, "", 0, 0);
    $pdf->SetFont('Arial', "", 10);
    $pdf->Cell(0, 6, utf8_decode("Humberto de Campos, 470 A - loja 01"), 0, 1, "R"); 

    $pdf->Cell(114, 6, "", 0, 0);
    $pdf->Cell(0, 6, utf8_decode("Jardim Limoeiro, Serra - ES, 29164-034"), 0, 1, "R");

    $pdf->Cell(0, 6, "", 0, 1); 

    $pdf->SetFont('Arial', "B", 16);
    $pdf->Cell(189, 20, utf8_decode("ORÇAMENTO"), 0, 1,);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(189, 6, utf8_decode($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()), 0, 1);

    $pdf->Cell(189, 6, utf8_decode($order->get_billing_address_1() . ', ' . $order->get_billing_address_2()), 0, 1);

    $pdf->Cell(202, 6, utf8_decode($order->get_billing_city()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Vendedor:"), 0, 0);
    $pdf->Cell(25, 6, utf8_decode(get_field('vendedor' , $order->get_order_number())), 0, 1);

    $pdf->Cell(202, 6, utf8_decode("Espírito Santo"), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Data da Fatura:"), 0, 0); 
    $pdf->Cell(25, 6, utf8_decode(date('d/m/Y')), 0, 1); 

    $pdf->Cell(202, 6,utf8_decode($order->get_billing_phone()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Número do Pedido:"), 0, 0); 
    $pdf->Cell(25, 6, utf8_decode($order->get_order_number()), 0, 1); 

    $pdf->Cell(202, 6, utf8_decode($order->get_billing_email()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Data do Pedido:"), 0, 0); 
    $pdf->Cell(25, 6, utf8_decode($order->get_date_created()->format('d/m/Y')), 0, 1); 

    $pdf->Cell(0, 20, "", 0, 1); 

    $pdf->SetFillColor(0,0,0);
    $pdf->SetTextColor(255,255,255);

    $pdf->Cell(30,16,utf8_decode("imagem"),1,0, "C", true); 
    $pdf->Cell(110,16,utf8_decode("Produto"),1, 0, "C", true);
    $pdf->Cell(30,16, utf8_decode("Quantidade"),1,0, "C", true);
    $pdf->Cell(30,16, utf8_decode("Preço"),1,0, "C", true);
    $pdf->Cell(77,16, utf8_decode("Entrega"),1,1, "C", true);

    foreach($order->get_items() as $item_id => $item){
        $product = $item->get_product();
        $product_name = $product->get_name();
        $product_quantity = $item->get_quantity();
        $product_price = $product->get_price();
        $product_id = $product->get_id();

        $formatted_price = 'R$ ' . number_format($product_price, 2, ',', '.');
        $delivery_type;
        
        $table_product = wc_get_product( $item["product_id"] );

        // Verifique se o produto é uma variante
        if ( $table_product->is_type( 'variable' ) ) {
            // Obtenha todas as variantes do produto
            $variation_id = $item["variation_id"];

            if(get_the_post_thumbnail_url($variation_id)) {
                $product_image_url = get_the_post_thumbnail_url($variation_id);
            } else {
                $product_image_url = plugin_dir_url(__FILE__) . '../assets/images/woocommerce-placeholder.png';
            }

            $meta_data = get_post_meta( $variation_id );
            $valor = $meta_data["_pronta_entrega_encomenda"][0];

            // echo $valor;
            if ($valor === "pronta_entrega") {
                $delivery_type = "Pronta Entrega";
            } else if ($valor === "encomenda") {
                $delivery_type = "Sob Encomenda";
            } else {
                $delivery_type = "Não definido";
            }
        } else {
            if(get_the_post_thumbnail_url($product_id)) {
                $product_image_url = get_the_post_thumbnail_url($product_id);
            } else {
                $product_image_url = plugin_dir_url(__FILE__) . '../assets/images/woocommerce-placeholder.png';
            }

            $meta_data = get_post_meta( $product_id );
            $valor = $meta_data["_yith_wcbm_badge_ids"][0];

            if ($valor === "277") {
                $delivery_type = "Pronta Entrega";
            } else if ($valor === "275") {
                $delivery_type = "Sob Encomenda";
            } else {
                $delivery_type = "Não definido";
            }
        }

        

        $cellWidth=110;
        $cellHeight=16;

        
        if($pdf->GetStringWidth($product_name) < $cellWidth){
            $line=1;
        }else{
            $textLength=strlen($product_name);	
            $errMargin=10;		
            $startChar=0;		
            $maxChar=0;			
            $textArray=array();	
            $tmpString="";		
            
            while($startChar < $textLength){ 
                while( 
                $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                ($startChar+$maxChar) < $textLength ) {
                    $maxChar++;
                    $tmpString=substr($product_name,$startChar,$maxChar);
                }
                $startChar=$startChar+$maxChar;
                array_push($textArray,$tmpString);
                
                $maxChar=0;
                $tmpString='';
            }
            $line=count($textArray);
        }


        $pdf->SetTextColor(0,0,0);

        
        $pdf->Cell(30,($line * $cellHeight),"",1,0, "C");
        
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();

        $pdf->Image($product_image_url, 20, ($pdf->GetY() + 2), 12, 12);
        
        $pdf->MultiCell($cellWidth,$cellHeight,utf8_decode($product_name),1, "C");
        
        $pdf->SetXY($xPos + $cellWidth , $yPos);

        $pdf->Cell(30,($line * $cellHeight), utf8_decode($product_quantity),1,0, "C");
        
        $pdf->Cell(30,($line * $cellHeight),utf8_decode($formatted_price),1,0, "C");

        $pdf->Cell(77,($line * $cellHeight),utf8_decode($delivery_type),1,1, "C");
        
    }

    $pdf->Ln(16);

    $pdf->SetFont('Arial', "B", 16);
    $pdf->Cell(0, 20, utf8_decode("Detalhes do pedido"), 0, 1,);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(90, 5,utf8_decode('Validade da proposta:'),0,0);
    $pdf->Cell(90, 5,utf8_decode(get_field('validade_da_proposta' , $order->get_order_number())),0,1);

    $pdf->Cell(90, 5,utf8_decode('Forma de pagamento:'),0,0);
    $pdf->Cell(90, 5,utf8_decode(get_field('metodo_de_pagamento' , $order->get_order_number())),0,1);

    $pdf->Cell(90, 5,utf8_decode('Prazo de entrega:'),0,0);
    $pdf->Cell(90, 5,utf8_decode(get_field('prazo_de_entrega' , $order->get_order_number())) ,0,1);

    $formatted_value = 'R$ ' . number_format(get_field('valor_total' , $order->get_order_number()), 2, ',', '.');

    $pdf->Cell(90, 5,utf8_decode('Valor total:'),0,0);
    $pdf->SetFont('Arial', "B", 12);
    $pdf->Cell(90, 5,utf8_decode($formatted_value),0,1);

    $pdf->Ln(16);

    $pdf->SetFont('Arial', "B", 16);
    $pdf->Cell(0, 20, utf8_decode("Observações"), 0, 1,);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(90, 5,utf8_decode(get_field('observacoes' , $order->get_order_number())),0,1);

    $filename = 'Orcamento-' . $order->get_date_created()->format('d-m-Y') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $pdf->Output('D', $filename);

    die();
}




add_action('wp_ajax_generate_pdf', 'generate_pdf');
add_action('wp_ajax_nopriv_generate_pdf', 'generate_pdf');
?>