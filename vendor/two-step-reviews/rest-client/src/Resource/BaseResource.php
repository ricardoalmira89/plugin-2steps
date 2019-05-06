<?php

namespace TwoStepReviews\Resource;

use TwoStepReviews\AuthManager;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: ralmira
 * Date: 8/10/2018
 * Time: 8:38 AM
 */
abstract class BaseResource
{
    private $path = 'api';
    protected $client;
    protected $endpoint;
    private $authManager;

    public function __construct($path, AuthManager $authManager)
    {
        $this->endpoint = $authManager->getApi().'/'.$this->path.'/'.$path;
        $this->client = new Client();
        $this->authManager = $authManager;
    }

    protected function authorizeRequest(&$options){
        $this->authManager->auth();
        $options['headers']['Authorization'] = "Bearer ".$this->authManager->getAccessToken();
    }

    public function index($options = []){

        $endpoint = sprintf("%s?%s", $this->endpoint, http_build_query($options));
        $this->authorizeRequest($options);

        $res = $this->client->get($endpoint, $options);
        return json_decode($res->getBody()->getContents());
    }

    public function show($id, $options = []){

        $endpoint = sprintf("%s?%s", $this->endpoint.'/'.$id, http_build_query($options));
        $this->authorizeRequest($options);

        $res = $this->client->get($endpoint, $options);
        return json_decode($res->getBody()->getContents());
    }

    public function delete($id, $options = []){
        $this->authorizeRequest($options);
        $res = $this->client->delete($this->endpoint.'/'.$id, $options);
        return json_decode($res->getBody()->getContents());
    }

    public function create($data = []){
        $options = [];
        $this->authorizeRequest($options);
        $options['headers']['Content-Type'] = "application/json";
        $options['body'] = json_encode($data);

        $res = $this->client->post($this->endpoint."/", $options);
        return json_decode($res->getBody()->getContents());
    }

    public function update($id, $data = []){
        $options = [];
        $this->authorizeRequest($options);
        $options['headers']['Content-Type'] = "application/json";
        $options['body'] = json_encode($data);

        $res = $this->client->put($this->endpoint."/".$id, $options);
        return json_decode($res->getBody()->getContents());
    }

}