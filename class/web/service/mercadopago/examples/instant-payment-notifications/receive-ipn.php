<?php

/**
 * MercadoPago SDK
 * Receive IPN
 * @date 2012/03/29
 * @author hcasatti
 */
// Include Mercadopago library
require_once "../../lib/mercadopago.php";

// Create an instance with your MercadoPago credentials (CLIENT_ID and CLIENT_SECRET): 
// Argentina: https://www.mercadopago.com/mla/herramientas/aplicaciones 
// Brasil: https://www.mercadopago.com/mlb/ferramentas/aplicacoes
// Mexico: https://www.mercadopago.com/mlm/herramientas/aplicaciones 
// Venezuela: https://www.mercadopago.com/mlv/herramientas/aplicaciones 
$mp = new MP("CLIENT_ID", "CLIENT_SECRET");

// Get the payment reported by the IPN. Glossary of attributes response in https://developers.mercadopago.com
$payment_info = $mp->get_payment_info($_GET["id"]);

// Show payment information
if ($payment_info["status"] == 200) {
    print_r($payment_info["response"]);
}
?>
