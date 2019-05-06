<?php

namespace TwoStepReviews\Resource;

use TwoStepReviews\AuthManager;

class Review extends BaseResource
{

    public function __construct(AuthManager $authManager)
    {
        parent::__construct("reviews", $authManager);
    }

}