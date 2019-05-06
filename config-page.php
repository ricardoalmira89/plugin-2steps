<?php

use Timber\Timber;

include_once 'connect.php';
defined('ABSPATH') || exit;

$result = connect();
$company = null;
$dash = null;

if ($result['connected']){
    //-- Get company ---
    $client = new \TwoStepReviews\Client();
    $res = $client->get('company')->index();
    $company = $res->data->results[0];
    $dash = $client->get('dashboard')->getDashBoard();
}


Timber::render('assets/templates/config-page.html.twig', array(
    'connected' => $result['connected'],
    'dash' => $dash,
    'url' => esc_url( Two_Step_Reviews_App::get_page_url('config-page.php') )
));
