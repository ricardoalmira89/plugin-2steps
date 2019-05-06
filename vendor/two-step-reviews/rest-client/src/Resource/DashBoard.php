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
        $this->authorizeRequest($options);
        $res = $this->client->get($this->endpoint."/dashboard", $options);
        return json_decode($res->getBody()->getContents());
    }

}