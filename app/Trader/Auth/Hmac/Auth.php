<?php

namespace Trader\Auth\Hmac;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class Auth {

    private $_method;
    private $_uri;
    private $_params;
    private $_auth_params;

    public function __construct($method, $uri, array $params)
    {

        $this->_method = $method;
        $this->_uri = $uri;

        foreach($params as $key => $val) {
            //$k = strtolower($key);
            $k = $key;
            substr($k, 0, 5) == 'auth_' ? $this->_auth_params[$k] = $val : $this->_params[$k] = $val;
        }
    }

    public function authenticate(Token $token)
    {
        $request = new Request($this->_method, $this->_uri, $this->_params);
        $signature = $request->sign($token);

        $this->validateVersion($this->_auth_params, $signature);
        $this->validateKey($this->_auth_params, $signature);
        $this->validateTimestamp($this->_auth_params, $signature);
        $this->validateSignature($this->_auth_params, $signature);

        return true;
    }

    private function validateKey(array $auth, array $sig)
    {
        if(!isset($auth['auth_key'])) {
            throw new AuthenticationException('Key not found');
        }
        if($auth['auth_key'] !== $sig['auth_key']) {
            throw new AuthenticationException('Invalid Key');
        }

        return true;
    }

    private function validateVersion(array $auth, array $sig)
    {
        if(!isset($auth['auth_version'])) {
            throw new AuthenticationException('Version not found');
        }
        if($auth['auth_version'] !== $sig['auth_version']) {
            throw new AuthenticationException('Invalid Version');
        }

        return true;
    }

    private function validateTimestamp(array $auth, array $sig, $gracePeriod = 600)
    {
        if(!isset($auth['auth_timestamp'])) {
            throw new AuthenticationException('Timestamp not found');
        }
        if($auth['auth_timestamp'] - time() >= $gracePeriod) {
            throw new AuthenticationException('Invalid Timestamp');
        }

        return true;
    }

    private function validateSignature(array $auth, array $sig)
    {
        if(!isset($auth['auth_signature'])) {
            throw new AuthenticationException('Signature not found');
        }
        if($auth['auth_signature'] !== $sig['auth_signature']) {
            throw new AuthenticationException('Invalid Signature');
        }

        return true;
    }
} 