<?php
namespace Trader\Auth\Hmac {

    // mock time
    function time()
    {
        return 1427724158;
    }
}

namespace {

    class AuthTest extends PHPUnit_Framework_TestCase {

        private $params;
        private $token;
        private $secret = 'e249c439ed7697df2a4b045d97d4b9b7e1854c3ff8dd668c779013653913572e';

        public function setUp()
        {
            $this->params = [
                'auth_version'  => '1.0',
                'auth_key'      => 'test',
                'auth_timestamp'=> 1427724158,
                'auth_signature'=> 'ec9783df15062176a54afb3e7e10cfc95d8b0d179b46367bdc3e2dbb0f183329',
                'name'          => 'tester'
            ];

            $this->token = new \Trader\Auth\Hmac\Token($this->params['auth_key'], $this->secret);
        }

        public function testSuccessfulVerification()
        {
            $auth = new \Trader\Auth\Hmac\Auth('POST','test',$this->params);
            $result = $auth->authenticate($this->token);

            $this->assertTrue($result);
        }

        public function testFailOnVersion()
        {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationException');

            $this->params['auth_version'] = '2.0';

            $auth = new \Trader\Auth\Hmac\Auth('POST','test',$this->params);
            $result = $auth->authenticate($this->token);

        }

        public function testFailOnKey()
        {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationException');

            $this->params['auth_key'] = '2.0';

            $auth = new \Trader\Auth\Hmac\Auth('POST','test',$this->params);
            $result = $auth->authenticate($this->token);

        }

        public function testFailOnTime()
        {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationException');

            $this->params['auth_timestamp'] = 1427725158;

            $auth = new \Trader\Auth\Hmac\Auth('POST','test',$this->params);
            $result = $auth->authenticate($this->token);

        }

        public function testFailOnSignature()
        {
            $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationException');

            $this->params['auth_signature'] = '1';

            $auth = new \Trader\Auth\Hmac\Auth('POST','test',$this->params);
            $result = $auth->authenticate($this->token);

        }
    }
}