<?php
add_action('wp_ajax_generate_pdf_ajax', 'generate_pdf_ajax');
add_action('wp_ajax_nopriv_generate_pdf_ajax', 'generate_pdf_ajax');

function generate_pdf_ajax(){
    $order_id = $_POST['order_id'];

    $order = wc_get_order($order_id);

    $pdf_date = $order->get_date_created()->format('d-m-Y');

    $pdf_url = order_pdf_generate($order);

    // Envie uma resposta JSON de sucesso
    wp_send_json_success(array(
        'pdf_url' => $pdf_url,
        'pdf_date' => $pdf_date
    ));
    
    wp_die();
}

function order_pdf_generate($order) {
    require(plugin_dir_path(__FILE__) . '../vendor/setasign/fpdf/fpdf.php');
    ob_clean();
    $logo_path = plugin_dir_url(__FILE__) . '../assets/images/mirantelogo.png';

    $pdf = new FPDF('P','mm','A4');

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
    $pdf->Cell(189, 20, utf8_decode("FATURA"), 0, 1,);

    $pdf->SetFont('Arial', "", 12);
    $pdf->Cell(189, 6, utf8_decode($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()), 0, 1);

    $pdf->Cell(189, 6, utf8_decode($order->get_billing_address_1() . ', ' . $order->get_billing_address_2()), 0, 1);

    $pdf->Cell(114, 6, utf8_decode($order->get_billing_city()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Número da Fatura:"), 0, 0);
    $pdf->Cell(25, 6, utf8_decode("8"), 0, 1); 

    $pdf->Cell(114, 6, utf8_decode("Espírito Santo"), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Data da Fatura:"), 0, 0); 
    $pdf->Cell(25, 6, utf8_decode($order->get_date_created()->format('d/m/Y')), 0, 1); 

    $pdf->Cell(114, 6,utf8_decode($order->get_billing_phone()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Número do Pedido:"), 0, 0); 
    $pdf->Cell(25, 6, utf8_decode($order->get_order_number()), 0, 1); 

    $pdf->Cell(114, 6, utf8_decode($order->get_billing_email()), 0, 0);
    $pdf->Cell(50, 6, utf8_decode("Data do Pedido:"), 0, 0); 
    $pdf->Cell(25, 6, utf8_decode($order->get_date_created()->format('d/m/Y')), 0, 1); 

    $pdf->Cell(0, 20, "", 0, 1); 

    foreach($order->get_items() as $item_id => $item){
        $product = $item->get_product();
        $product_name = $product->get_name();
        $product_quantity = $item->get_quantity();
        $product_price = $product->get_price();
        $product_id = $product->get_id();

        $product_image_url = get_the_post_thumbnail_url($product_id);
        $formatted_price = 'R$ ' . number_format($product_price, 2, ',', '.');

        $cellWidth=80;
        $cellHeight=5;
        
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

        $pdf->SetFillColor(0,0,0);
        $pdf->SetTextColor(255,255,255);
        
        $pdf->Cell(40,($line * $cellHeight),utf8_decode("imagem"),1,0, "C", true); 
        
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();
        $pdf->MultiCell($cellWidth,($line * $cellHeight),utf8_decode("Produto"),1, "C", true);
        
        $pdf->SetXY($xPos + $cellWidth , $yPos);

        $pdf->Cell(30,($line * $cellHeight), utf8_decode("Quantidade"),1,0, "C", true);
        
        $pdf->Cell(40,($line * $cellHeight), utf8_decode("Preço"),1,1, "C", true); 

        $pdf->SetTextColor(0,0,0);
        
        $pdf->Cell(40,($line * $cellHeight),$pdf->Image($product_image_url, null, null, 20),1,0, "C"); 
        
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();
        $pdf->MultiCell($cellWidth,$cellHeight,utf8_decode($product_name),1, "C");
        
        $pdf->SetXY($xPos + $cellWidth , $yPos);

        $pdf->Cell(30,($line * $cellHeight), utf8_decode($product_quantity),1,0, "C");
        
        $pdf->Cell(40,($line * $cellHeight),utf8_decode($formatted_price),1,1, "C"); 
        
    }

    $filename = 'Orcamento-' . $order->get_date_created()->format('d-m-Y') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $pdf->Output('F', $filename);
    return $filename;

    die();
}

?>