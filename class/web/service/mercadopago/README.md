# MercadoPago SDK module for Payments integration

* [Usage](#usage)
* [Using MercadoPago Checkout](#checkout)
* [Using MercadoPago Payment collection](#payments)

<a name="usage"></a>
## Usage:

1. Copy lib/mercadopago.php to your project desired folder.
2. Copy lib/cacert.pem to the same folder (for SSL access to MercadoPago APIs).

* Get your **CLIENT_ID** and **CLIENT_SECRET** in the following address:
    * Argentina: [https://www.mercadopago.com/mla/herramientas/aplicaciones](https://www.mercadopago.com/mla/herramientas/aplicaciones)
    * Brazil: [https://www.mercadopago.com/mlb/ferramentas/aplicacoes](https://www.mercadopago.com/mlb/ferramentas/aplicacoes)
    * Mexico: [https://www.mercadopago.com/mlm/herramientas/aplicaciones](https://www.mercadopago.com/mlm/herramientas/aplicaciones)
    * Venezuela: [https://www.mercadopago.com/mlv/herramientas/aplicaciones](https://www.mercadopago.com/mlv/herramientas/aplicaciones)
    * Colombia: [https://www.mercadopago.com/mco/herramientas/aplicaciones](https://www.mercadopago.com/mco/herramientas/aplicaciones)

```php
require_once "mercadopago.php";

$mp = new MP ("CLIENT_ID", "CLIENT_SECRET");
```

### Get your Access Token:

```php
$accessToken = $mp->get_access_token();

print_r ($accessToken);
```

<a name="checkout"></a>
## Using MercadoPago Checkout

### Get an existent Checkout preference:

```php
$preferenceResult = $mp->get_preference("PREFERENCE_ID");

print_r ($preferenceResult);
```

### Create a Checkout preference:

```php
$preference = array (
    "items" => array (
        array (
            "title" => "Test",
            "quantity" => 1,
            "currency_id" => "USD",
            "unit_price" => 10.4
        )
    )
);

$preferenceResult = $mp->create_preference($preference);

print_r ($preferenceResult);
```
<a href="http://developers.mercadopago.com/documentacion/recibir-pagos#glossary">Others items to use</a>

### Update an existent Checkout preference:

```php
$preference = array (
    "items" => array (
        array (
            "title" => "Test Modified",
            "quantity" => 1,
            "currency_id" => "USD",
            "unit_price" => 20.4
        )
    )
);

$preferenceResult = $mp->update_preference("PREFERENCE_ID", $preference);

print_r ($preferenceResult);
```

<a name="payments"></a>
## Using MercadoPago Payment

###Searching:

```php
$filters = array (
        "id"=>null,
        "site_id"=>null,
        "external_reference"=>null
    );

$searchResult = $mp->search_payment ($filters);

print_r ($searchResult);
```
<a href="http://developers.mercadopago.com/documentacion/busqueda-de-pagos-recibidos">More search examples</a>
### Receiving IPN notification:

* Go to **Mercadopago IPN configuration**:
    * Argentina: [https://www.mercadopago.com/mla/herramientas/notificaciones](https://www.mercadopago.com/mla/herramientas/notificaciones)
    * Brasil: [https://www.mercadopago.com/mlb/ferramentas/notificacoes](https://www.mercadopago.com/mlb/ferramentas/notificacoes)
    * Mexico: [https://www.mercadopago.com/mlm/herramientas/notificaciones](https://www.mercadopago.com/mlm/herramientas/notificaciones)
    * Venezuela: [https://www.mercadopago.com/mlv/herramientas/notificaciones](https://www.mercadopago.com/mlv/herramientas/notificaciones)
    * Colombia: [https://www.mercadopago.com/mco/herramientas/notificaciones](https://www.mercadopago.com/mco/herramientas/notificaciones)

```php
require_once "mercadopago.php";

header("Content-type: text/plain");

$mp = new MP ("CLIENT_ID", "CLIENT_SECRET");
$paymentInfo = $mp->get_payment_info ($_GET["id"]);

header ("", true, $paymentInfo["status"]);

print_r ($paymentInfo);
```

### Cancel (only for pending payments):

```php
$result = $mp->cancel_payment($_GET["ID"]);

// Show result
print_r ($result);
```

### Refund (only for accredited payments):

```php
$result = $mp->refund_payment($_GET["ID"]);

// Show result
print_r ($result);
```
<a href="http://developers.mercadopago.com/documentacion/devolucion-y-cancelacion">About Cancel & Refund </a>
