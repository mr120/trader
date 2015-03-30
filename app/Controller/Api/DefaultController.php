<?php

namespace Controller\Api;

use Silex\Application;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController
{

    /**
     * @param Application $app
     * @return mixed
     *
     * Receive json array of trade message
     */
    public function indexAction(Application $app)
    {
        // get from db
        $token = new \Trader\Auth\Hmac\Token('trader_account','e249c439ed7697df2a4b045d97d4b9b7e1854c3ff8dd668c779013653913572e');

        $params = $app['request']->request->all();

        $auth = new \Trader\Auth\Hmac\Auth($app['request']->getMethod(),'/api/v1/trade/new',$params);

        try {
            $auth->authenticate($token);

        } catch(AuthenticationException $e){
            //var_dump($e->getMessage());
            $returnArray['success'] = false;
            $returnArray['status'] = $e->getMessage();

            return $app->json($returnArray, 403);
        }

        // load trade model
        $messageModel = new \Trader\Message($app['orm.em'], $app['validator']);

        // get message from post vars
        $messageParam = $app['request']->get('message');

        // validate message and create new message entity on success
        $message = $messageModel->createMessage($messageParam);

        if($message) {
            $ohlc = $messageModel->createStatData($messageParam);

            $em = $app['orm.em'];

            if($ohlc) {
                $em->persist($ohlc);
            }

            $em->persist($message);

            $em->flush();

            $return_array['success'] = true;
            $return_array['status'] = "";
            $return_array['object'] = $message;

            return $app->json($return_array, 201);
        }

        $returnArray['success'] = false;
        $returnArray['status'] = "Message parameters invalid";
        $returnArray['object'] = $message;

        return $app->json($returnArray, 500);



    }

    public function getAction(Application $app)
    {
        $this->isJson = true;

        $messageRepo = $app['orm.em']->getRepository('\Trader\Entity\Message');
        $messages = $messageRepo->getLatestData();

        return $app->json($messages, 201);
    }

    public function getPairAction(Application $app, $from, $to)
    {
        $this->isJson = true;

        $messageRepo = $app['orm.em']->getRepository('\Trader\Entity\Message');
        $messages = $messageRepo->getDataByPair($from, $to);

        return $app->json($messages, 201);
    }

    public function getPairStatsAction(Application $app, $from, $to)
    {
        $this->isJson = true;

        $ohlcRepo = $app['orm.em']->getRepository('\Trader\Entity\Ohlc');
        $ohlc = $ohlcRepo->getDataByPair($from, $to);

        return $app->json($ohlc, 201);
    }

    public function sendMessageAction(Application $app)
    {
        $this->isJson = true;

        $message = [
            'message' => [
                'userId' => '1',
                'currencyFrom' => $app['request']->get('currencyFrom'),
                'currencyTo' => $app['request']->get('currencyTo'),
                'amountSell' => $app['request']->get('amountSell'),
                'amountBuy' => $app['request']->get('amountBuy'),
                'rate' =>  $app['request']->get('amountBuy') / $app['request']->get('amountSell'),
                'timePlaced' => date('Y-m-d H:i:s'),
                'originatingCountry' => 'GB'
            ]
        ];

        $token = new \Trader\Auth\Hmac\Token('trader_account','e249c439ed7697df2a4b045d97d4b9b7e1854c3ff8dd668c779013653913572e');

        $request = new \Trader\Auth\Hmac\Request($app['request']->getMethod(),'/api/v1/trade/new',$message);
        $auth_params = $request->sign($token);

        $params = array_merge($auth_params, $message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://trader.dev/api/v1/trade/new');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $data = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        curl_close($ch);
        if($status_code == 201) {
            return $app->json([], 201);
        }

        return $app->json([], $status_code);

    }
}