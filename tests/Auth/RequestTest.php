<?php

use Trader\Auth\Hmac\Request;
use Trader\Auth\Hmac\Token;

class RequestTest extends PHPUnit_Framework_TestCase
{
    private $token;

    public function setUp()
    {
        $this->token = new Token('key','secret');
    }

    public function testRequestSignature()
    {
        $params = ['name' => 'tester'];
        $request = new Request('POST','test', $params);

        $sig = $request->getSignature($this->token);

        $this->assertEquals('0e1b7031d713e2d354db2336dc0ce2cb8f26d7b4d6504715ae2b401ce6de7593', $sig);

    }

    public function testRequestSign()
    {
        $params = ['name' => 'tester'];
        $request = new Request('POST','test', $params);

        $auth_req = $request->sign($this->token);

        $this->assertEquals('0e1b7031d713e2d354db2336dc0ce2cb8f26d7b4d6504715ae2b401ce6de7593', $auth_req['auth_signature']);
        $this->assertEquals('key', $auth_req['auth_key']);
        $this->assertEquals(1427724158, $auth_req['auth_timestamp']);
        $this->assertEquals('1.0', $auth_req['auth_version']);
    }
}