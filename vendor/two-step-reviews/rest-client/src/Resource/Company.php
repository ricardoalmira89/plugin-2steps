<?php

namespace TwoStepReviews\Resource;

use TwoStepReviews\AuthManager;

class Company extends BaseResource
{

    public function __construct(AuthManager $authManager)
    {
        parent::__construct("companies", $authManager);
    }

}