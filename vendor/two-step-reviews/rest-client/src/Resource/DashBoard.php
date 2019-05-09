<?php

namespace TwoStepReviews\Resource;

use TwoStepReviews\AuthManager;

class DashBoard extends BaseResource
{

    public function __construct(AuthManager $authManager)
    {
        parent::__construct("stats", $authManager);
    }

    public function getDashBoard($options = []){
        $endpoint = sprintf("%s?%s", $this->endpoint."/dashboard", http_build_query($options));
        $this->authorizeRequest($options);

        $res = $this->client->get($endpoint, $options);
        return json_decode($res->getBody()->getContents());
    }

}