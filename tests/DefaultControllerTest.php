<?php

use Silex\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $_secret = 'e249c439ed7697df2a4b045d97d4b9b7e1854c3ff8dd668c779013653913572e';
    private $_key = 'trader_account';

    public function createApplication()
    {
        $app = new App();

        return $app;
    }

    public function testAccess()
    {
        $message = [
            'message' => [
                'userId' => '1',
                'currencyFrom' => 'EUR',
                'currencyTo' => 'GBP',
                'amountSell' => 1000,
                'amountBuy' => 747.10,
                'rate' => 0.740,
                'timePlaced' => date('Y-m-d H:i:s'),
                'originatingCountry' => 'FR'
            ]
        ];

        $token = new \Trader\Auth\Hmac\Token($this->_key, $this->_secret);
        $request = new \Trader\Auth\Hmac\Request('POST','/api/v1/trade/new',$message);
        $signed_request = $request->sign($token);

        $params = array_merge($signed_request, $message);

        $client = $this->createClient();

        $client->request('POST', '/api/v1/trade/new', $params);

        $this->assertEquals(
            201,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testAccessFailed()
    {
        $message = [
            'message' => [
                'userId' => '1',
                'currencyFrom' => 'EUR',
                'currencyTo' => 'GBP',
                'amountSell' => 1000,
                'amountBuy' => 747.10,
                'rate' => 0.740,
                'timePlaced' => date('Y-m-d H:i:s'),
            ]
        ];

        $token = new \Trader\Auth\Hmac\Token($this->_key, $this->_secret);
        $request = new \Trader\Auth\Hmac\Request('POST','/api/v1/trade/new',$message);
        $signed_request = $request->sign($token);

        $params = array_merge($signed_request, $message);

        $client = $this->createClient();

        $client->request('POST', '/api/v1/trade/new', $params);

        $this->assertEquals(
            500,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testAccessDenied()
    {
        $message = array(
            'message' => array(
                'userId' => '1',
                'currencyFrom' => 'EUR',
                'currencyTo' => 'GBP',
                'amountSell' => 1000,
                'amountBuy' => 747.10,
                'rate' => 0.740,
                'timePlaced' => date('Y-m-d H:i:s'),
                'originatingCountry' => 'FR'
            )
        );

        $token = new \Trader\Auth\Hmac\Token('trader_accounter', $this->_secret);
        $request = new \Trader\Auth\Hmac\Request('POST','/api/v1/trade/new',$message);
        $signed_request = $request->sign($token);

        $params = array_merge($signed_request, $message);

        $client = $this->createClient();

        $client->request('POST', '/api/v1/trade/new', $params);

        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode()
        );
    }

}