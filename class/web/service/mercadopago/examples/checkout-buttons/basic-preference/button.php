<?php
require_once "../../../lib/mercadopago.php";

$mp = new MP("CLIENT_ID", "CLIENT_SECRET");

$preference = array(
    "items" => array(
        array(
            "title" => "sdk-php",
            "quantity" => 1,
            "currency_id" => "ARS",
            "unit_price" => 10.2
        )
    )
);

$preferenceResult = $mp->create_preference($preference);
?>

<!doctype html>
<html>
    <head>
        <title>MercadoPago SDK - Create Preference and Show Checkout Example</title>
    </head>
    <body>
       	<a href="<?php echo $preferenceResult["response"]["init_point"]; ?>" name="MP-Checkout" class="orange-ar-m-sq-arall">Pay</a>
        <script type="text/javascript" src="http://mp-tools.mlstatic.com/buttons/render.js"></script>
    </body>
</html>
