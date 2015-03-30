<?php

class TokenTest extends PHPUnit_Framework_TestCase {

    public function testCanCreateToken()
    {
        $key = 'sample_key';
        $secret = 'sample_secret';
        $token = new \Trader\Auth\Hmac\Token($key,$secret);

        $this->assertEquals($token->getSecret(), $secret);
        $this->assertEquals($token->getKey(), $key);
    }
}
 