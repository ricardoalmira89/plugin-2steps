<?php
/**
 * Created by PhpStorm.
 * User: ralmira
 * Date: 8/10/2018
 * Time: 11:29 AM
 */

namespace TwoStepReviews;

use TwoStepReviews\Resource\BaseResource;
use Alm\AlmArray;
use Alm\AlmValidator;
use TwoStepReviews\Resource\Company;
use TwoStepReviews\Resource\DashBoard;
use TwoStepReviews\Resource\Review;

class Client
{

    public $authManager;
    public $recursos;

    public function __construct($options = [])
    {
        $this->authManager = new AuthManager($options);
        $this->recursos = array(
            'review' => new Review($this->authManager),
            'company' => new Company($this->authManager),
            'dashboard' => new DashBoard($this->authManager),
        );
    }

    /**
     * Obtiene un recurso
     *
     * @param $resourceName
     * @return BaseResource
     */
    public function get($resourceName){
        return AlmArray::get($this->recursos, $resourceName);
    }

}