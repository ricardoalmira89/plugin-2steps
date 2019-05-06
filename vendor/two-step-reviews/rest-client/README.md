TwoStepReviews / Rest Client
===========================

License
===========================
This library is released under the [MIT license](LICENSE).

Instalation
===========================
composer require "TwoStepReviews/rest-client @dev"

Uso
===========================
<?php

use TwoStepReviews\Client;
require_once 'vendor/autoload.php';

$parameters = array(
    'api' => "http://api.2stepreviews.com.com",
    'client_id' => "your client id",
    'client_secret' => "your client secret",
    'username' => "your username",
    'password' => "your password"
);

$client = new Client($parameters);

// Get a review list
$response = $client->get('review')->index();
