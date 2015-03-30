<?php



class TradeTest extends PHPUnit_Framework_TestCase {

    public function testValidateMessageArray()
    {
        $app = new App();

        $test_message = [
            'userId' => '1',
            'currencyFrom' => 'EUR',
            'currencyTo' => 'GBP',
            'amountSell' => 100,
            'amountBuy' => 150,
            'rate' => 1.15,
            'timePlaced' => date('Y-m-d H:i:s'),
            'originatingCountry' => 'ete'
        ];

        $message_model = new \Trader\Message($app['orm.em'], $app['validator']);
        $errors = $message_model->validateArray($test_message);

        $this->assertEmpty($errors);
    }

    public function testSaveMessage()
    {
        $app = new App();

        $test_message = [
            'userId' => '1',
            'currencyFrom' => 'EUR',
            'currencyTo' => 'GBP',
            'amountSell' => 100,
            'amountBuy' => 150,
            'rate' => 1.15,
            'timePlaced' => date('Y-m-d H:i:s'),
            'originatingCountry' => 'ete'
        ];

        $message_model = new \Trader\Message($app['orm.em'], $app['validator']);
        $message = $message_model->createMessage($test_message);

        $this->assertInstanceOf('\Trader\Entity\Message', $message);

    }

    public function testSaveMessage_returnsFalseWithInvalidData()
    {
        $app = new App();

        $test_message = [
            'userId' => '1',
            'currencyFrom' => 'EUR',
            'currencyTo' => 'GBP',
            'amountSell' => 100,
            'amountBuy' => 150,
            'rate' => 1.15,
            'timePlaced' => date('Y-m-d H:i:s'),
        ];

        $message_model = new \Trader\Message($app['orm.em'], $app['validator']);
        $message = $message_model->createMessage($test_message);

        $this->assertFalse($message);

    }

    public function testCreateStatData_WhenDataIsOld()
    {
        $dateTime = $this->getDateTime();
        $dateTime->modify('-15 minutes');

        $ohlcRepository = \Mockery::mock('\Doctrine\ORM\EntityRepository',
            [
                'getLatestDataByPair' => ['dateAdded' => $dateTime]
            ]
        );

        $dateTime2 = clone($dateTime);
        $testData = [
            'currencyFrom' => 'EUR',
            'currencyTo' => 'GBP',
            'open' => 60,
            'close' => 60,
            'high' => 60,
            'low' => 60,
            'dateAdded'=>new \DateTime($dateTime2->modify('+2 minutes')->format('Y-m-d H:i:s'))
        ];
        $messageRepository = \Mockery::mock('\Doctrine\ORM\EntityRepository',
            [
                'getDataByPairOverTime' => $testData
            ]
        );

        $entityManager = \Mockery::mock('\Doctrine\ORM\EntityManager')
            ->shouldReceive('getRepository')
            ->andReturn($ohlcRepository, $messageRepository)
            ->mock();

        $testMessage = [
            'currencyFrom' => 'EUR',
            'currencyTo' => 'GBP',
            'amountSell' => 100,
            'amountBuy' => 150,
            'timePlaced' => date('Y-m-d H:i:s'),
        ];

        $validator = $this->validatorMock();
        $messageModel = new \Trader\Message($entityManager, $validator);
        $ohlc = $messageModel->createStatData($testMessage);

        $this->assertInstanceOf('\Trader\Entity\Ohlc', $ohlc);
        $this->assertEquals($dateTime->modify('+15 minutes')->format('Y-m-d H:i:s'), $ohlc->getDateAdded()->format('Y-m-d H:i:s'));
    }

    public function testCreateStatData_WhenDataUpToDate()
    {
        $ohlcRepository = \Mockery::mock('\Doctrine\ORM\EntityRepository',
            array(
                'getLatestDataByPair' => ['dateAdded' => date_create(date('Y-m-d H:i:s'))]
            )
        );

        $messageRepository = \Mockery::mock('\Doctrine\ORM\EntityRepository',
            array(
                'getDataByPairOverTime' => []
            )
        );

        $entityManager = \Mockery::mock('\Doctrine\ORM\EntityManager')
            ->shouldReceive('getRepository')
            ->andReturn($ohlcRepository, $messageRepository)
            ->mock();

        $testMessage = [
            'currencyFrom' => 'EUR',
            'currencyTo' => 'GBP',
            'amountSell' => 100,
            'amountBuy' => 150,
            'timePlaced' => date('Y-m-d H:i:s'),
        ];

        $validator = $this->validatorMock();
        $messageModel = new \Trader\Message($entityManager, $validator);
        $ohlc = $messageModel->createStatData($testMessage);

        $this->assertFalse($ohlc);
    }

    protected function validatorMock()
    {
        $validatorMock = \Mockery::mock('\Symfony\Component\Validator\Validator',
            [
                'validateValue' => [],
            ]
        );

        return $validatorMock;
    }

    protected function getDateTime($timeNow = null)
    {
        if(is_null($timeNow)) {
            $timeNow = new \DateTime();
        }

        $second = $timeNow->format("s");
        $timeNow->modify('-' . $second . ' seconds');

        $minutes = $timeNow->format("i");
        $minute = $minutes % 15;

        $timeNow->modify('-' . $minute . ' minutes');

        return $timeNow;
    }
}
 