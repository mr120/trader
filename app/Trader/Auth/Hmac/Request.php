<?php

namespace Trader\Auth\Hmac;



class Request {

    private $_method;

    private $_uri;

    private $_params;

    private $_version = '1.0';

    private $_auth_params = [
        'auth_version'  => null,
        'auth_key'      => null,
        'auth_timestamp'=> null,
        'auth_signature'=> null
    ];

    public function __construct($method, $uri, array $params)
    {

        $this->_method = $method;
        $this->_uri = $uri;
        $this->_params = $params;
    }

    public function sign(Token $token)
    {
        $this->_auth_params = [
            'auth_version'    => $this->_version,
            'auth_key'        => $token->getKey(),
            'auth_timestamp'  => time(),
            'auth_signature'  => $this->getSignature($token)
        ];

        return $this->_auth_params;
    }

    public function getSignature(Token $token)
    {
        return hash_hmac('sha256', $this->getJsonString(), $token->getSecret());
    }

    private function getJsonString()
    {

        $params = array_merge($this->_params, $this->_auth_params);

        ksort($params);

        $return = json_encode(
            [
                'method'      => $this->_method,
                'url'         => $this->_uri,
                'params'      => $params,
            ]
        );

        return $return;
    }
} 