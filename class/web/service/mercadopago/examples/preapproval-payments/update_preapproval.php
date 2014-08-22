<?php
require_once "lib/mercadopago.php";

$mp = new MP("CLIENT_ID", "CLIENT_SECRET");

$id = "PREAPPROVAL_ID";

$preapprovalPayment = array(
    "payer_email" => "my_customer@my_site.com",
    "back_url" => "http://www.example.com.ar",
    "reason" => "Suscripción mensual",
    "external_reference" => "OP-1234",
    "frequency" => 1,
    "frequency_type" => "months",
    "transaction_amount" => 60,
    "currency_id" => "ARS",
    "start_date" => "2013-10-10T14:58:11.778-03:00",
    "end_date" => "2013-12-10T14:58:11.778-03:00"
);

$preapprovalPaymentResult = $mp->update_preapproval_payment($id, $preapprovalPayment);
?>

<!doctype html>
<html>
    <head>
        <title>MercadoPago SDK - Create Preapproval Payment and Show Subscription Example</title>
    </head>
    <body>
       	<a href="<?php echo $preapprovalPaymentResult["response"]["init_point"]; ?>" name="MP-Checkout" class="orange-ar-m-sq-arall">Pay</a>
        <script type="text/javascript" src="http://mp-tools.mlstatic.com/buttons/render.js"></script>
    </body>
</html>
