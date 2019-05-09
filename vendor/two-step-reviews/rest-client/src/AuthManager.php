<?php
/**
 * Created by PhpStorm.
 * User: ralmira
 * Date: 5/10/2018
 * Time: 3:55 PM
 */

namespace TwoStepReviews;

use GuzzleHttp\Client;
use Alm\AlmArray;
use Alm\AlmValidator;

class AuthManager
{

    private $api;
    private $client_id;
    private $client_secret;
    private $username;
    private $password;
    private $client;

    private $access_token = null;
    private $refresh_token = null;
    private $expiresIn = null;
    private $expiresDate = null;

    public function __construct($data = [])
    {
        $this->username = AlmArray::get($data, 'username');
        $this->password = AlmArray::get($data, 'password');
        $this->client_id = AlmArray::get($data, 'client_id');;
        $this->client_secret = AlmArray::get($data, 'client_secret');
        $this->api = AlmArray::get($data, 'api');;
        $this->client = new Client();

        $this->loadToken();
    }

    public function isConnected(){
        return ($this->api != null && $this->access_token != null);
    }

    public function auth(){

        /**
         * Si el token expiro, refrescarlo
         */
        if ($this->isTokenExpired()){

            try{

                $res = $this->client->post($this->api."/oauth/v2/token", array(
                    'form_params' => array(
                        'grant_type' => 'refresh_token',
                        'client_id'  => $this->client_id,
                        'client_secret' => $this->client_secret,
                        'refresh_token' => $this->refresh_token
                    )
                ));

                $this->buildToken($res);

            } catch (\Exception $ex){

                // si no refresca, crear uno nuevo y ya
                $res = $this->client->post($this->api."/oauth/v2/token", array(
                    'form_params' => array(
                        'grant_type' => 'password',
                        'client_id'  => $this->client_id,
                        'client_secret' => $this->client_secret,
                        'username' => $this->username,
                        'password' => $this->password
                    )
                ));

                $this->buildToken($res);
            }
        }

        if (!$this->access_token){

            $res = $this->client->post($this->api."/oauth/v2/token", array(
                'form_params' => array(
                    'grant_type' => 'password',
                    'client_id'  => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'username' => $this->username,
                    'password' => $this->password
                )
            ));

            $this->buildToken($res);
        }

        return $this;
    }

    public function logout(){
        if (file_exists($this->getSessionFile()))
            unlink($this->getSessionFile());
    }

    /**
     * Establece el accessToken
     * @param $value
     * @return $this
     */
    public function setAccessToken($value){
        $this->access_token = $value;
        return $this;
    }

    /**
     * Devuelve el accessToken
     * @return mixed|null
     */
    public function getAccessToken(){
        return $this->access_token;
    }

    /**
     * Establece el refreshToken
     * @param $value
     * @return $this
     */
    public function setRefreshToken($value){
        $this->refresh_token = $value;
        return $this;
    }

    /**
     * Devuelve el refreshToken
     * @return mixed|null
     */
    public function getRefreshToken(){
        return $this->refresh_token;
    }

    /**
     * Determina si el token ha expirado
     * @return bool
     */
    public function isTokenExpired(){

        return  ($this->expiresDate)
            ? (new \DateTime('now') > $this->expiresDate)
            : false;
    }

    /**
     * Construye el token.
     *
     * @param $res
     * @return mixed
     */
    private function buildToken($res){
        $token = json_decode($res->getBody()->getContents(), true);
        if ($token){
            $this->access_token = $token['access_token'];
            $this->refresh_token = $token['refresh_token'];
            $this->expiresIn = $token['expires_in'];
            $this->expiresDate = $this->expiresAt($this->expiresIn);
            $token['expires_date'] = $this->expiresDate->format('Y-m-d H:i:s');
            $this->saveToken($token);
        }

        return $token;
    }

    /**
     * Establece la api a la que apunta el cliente
     * @param mixed $api
     * @return AuthManager
     */
    public function setApi($api)
    {
        $this->api = $api;
        return $this;
    }

    public function getApi(){
        return $this->api;
    }

    private function getSessionFile(){
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.'session-2steps';
    }

    /**
     * Almacena el token en la sesion
     * @param array $token
     */
    private function saveToken($token){

        $token['api'] = $this->getApi();
        $token['client_secret'] = $this->client_secret;
        $token['client_id'] = $this->client_id;

        AlmValidator::validate($token, array(
            'access_token' => 'req',
            'refresh_token' => 'req',
            'expires_in' => 'req',
            'expires_date' => 'req',
            'api' => 'req'
        ));

        AlmArray::saveToFile($token, $this->getSessionFile());
    }

    private function loadToken(){

        $token = AlmArray::loadFromFile( $this->getSessionFile());
        if (count($token) == 0)
            return;

        $this->access_token = AlmArray::get($token, 'access_token');
        $this->refresh_token = AlmArray::get($token, 'refresh_token');
        $this->expiresIn = AlmArray::get($token, 'expires_in');
        $this->expiresDate = new \DateTime(AlmArray::get($token, 'expires_date'));
        $this->api = AlmArray::get($token, 'api');
        $this->client_id =  AlmArray::get($token, 'client_id');
        $this->client_secret =  AlmArray::get($token, 'client_secret');
    }

    private function expiresAt($timestamp){
        $expires = new \DateTime('now');
        $expires->modify(sprintf("+%s hours", (($timestamp / 60) / 60 )));
        return $expires;
    }

}