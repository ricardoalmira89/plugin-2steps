<?php
include_once 'connect.php';

use TwoStepReviews\Client;
use Timber\Timber;

defined('ABSPATH') || exit;

/**
 * Gets limit in the request. Is the limit is something else than a number,
 * the function returns default limit 5
 * @return int|mixed|null
 */
function getLimit(){

    $limit = ($_GET['limit']) ? $_GET['limit'] : 5;
    preg_match('/\d+/', $limit, $matches );
    if (count($matches) == 0)
        return null;

    return (count($matches) == 0) ? 5 : $limit;
}

/**
 * Gets the url already paginated
 * @return string
 */
function getPaginatedUrl(){
    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url = add_query_arg('limit', getLimit() + 5, $url);

    return $url;
}

function showReviews()
{
    connect();
    $client = new Client();
    if (!$client->authManager->isConnected())
        return;

    $res = $client->get('review')->index(['limit' => getLimit()]);

    Timber::render('assets/templates/reviews-page.html.twig', array(
        'reviews' => $res->data->results,
        'url' => getPaginatedUrl()
    ));

}

function showFooter(){

    connect();
    $client = new Client();

    if (!$client->authManager->isConnected())
        return;

    $res = $client->get('dashboard')->getDashboard();

    Timber::render('assets/templates/reviews-footer.html.twig', array(
        'company_name' => $res->company[0]->name,
        'rating' => $res->after_2steps[0]->rating,
        'reviews' => $res->invites->received_rate->general->received
    ));
}

add_shortcode('reviews', 'showReviews');
add_shortcode('reviews-footer', 'showFooter');