<?php

namespace Trader;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator;

class Message {

    private $_em;
    private $_validator;

    public function __construct(ObjectManager $em, Validator $validator)
    {
        $this->_em = $em;
        $this->_validator = $validator;
    }

    public function validateArray($message)
    {
        $constraint = new Assert\Collection([
            'userId' => new Assert\NotBlank(),
            'currencyFrom' => new Assert\NotBlank(),
            'currencyTo' => new Assert\NotBlank(),
            'amountSell' => [new Assert\NotBlank(), new Assert\Type(['type'=>'numeric'])],
            'amountBuy' => [new Assert\NotBlank(), new Assert\Type(['type'=>'numeric'])],
            'rate' => new Assert\NotBlank(),
            'timePlaced' => new Assert\NotBlank(),
            'originatingCountry' => new Assert\NotBlank()
        ]);

        $errors = $this->_validator->validateValue($message, $constraint);

        return $errors;
    }

    public function createMessage($message_param)
    {

        if (count($this->validateArray($message_param)) == 0 && is_array($message_param)) {

            $message = new \Trader\Entity\Message;

            foreach($message_param as $field => $val){
                $func = 'set' . ucfirst($field);
                $message->$func($val);
            }

            $message->setTimePlaced(new \DateTime($message->getTimePlaced()));

            return $message;
        }

        return false;
    }

    /**
     * @param $stat
     * @return bool
     *
     *
     */
    private function _isOutOfDate($stat)
    {

        $lastTime = clone($stat['dateAdded']);
        $lastTime->modify('+15 minutes');
        $dateNow = new \DateTime();

        // if current time is more recent than last record plus 15mins
        if($dateNow > $lastTime) {
            return true;
        }

        return false;
    }

    public function createStatData($message_param)
    {
        if (is_array($message_param) && count($this->validateArray($message_param)) == 0) {

            // check if current time is > 15 mins after last stat
            $ohlc_repo = $this->_em->getRepository('\Trader\Entity\Ohlc');
            $stat = $ohlc_repo->getLatestDataByPair($message_param['currencyFrom'], $message_param['currencyTo']);

            if(!empty($stat)) {
                if($this->_isOutOfDate($stat)) {
                    $message_repo = $this->_em->getRepository('\Trader\Entity\Message');

                    $message = $message_repo->getDataByPairOverTime($message_param['currencyFrom'], $message_param['currencyTo'], $stat['dateAdded']);

                    // update ohlc for block after last existing one
                    if(!empty($message)) {
                        $dateAdded = clone($stat['dateAdded']);
                        $dateAdded->modify('+15 minutes');

                        $ohlc = new \Trader\Entity\Ohlc();
                        $ohlc->setCurrencyFrom($message_param['currencyFrom']);
                        $ohlc->setCurrencyTo($message_param['currencyTo']);
                        $ohlc->setOpen($message['open']);
                        $ohlc->setClose($message['close']);
                        $ohlc->setHigh($message['high']);
                        $ohlc->setLow($message['low']);
                        $ohlc->setDateAdded($dateAdded);

                        return $ohlc;
                    } else {
                        $timeNow = $this->getDateTime();
                        $message = $message_repo->getDataByPairOverTime($message_param['currencyFrom'], $message_param['currencyTo'], $timeNow);

                        if(!empty($message)) {

                            $ohlc = new \Trader\Entity\Ohlc();
                            $ohlc->setCurrencyFrom($message_param['currencyFrom']);
                            $ohlc->setCurrencyTo($message_param['currencyTo']);
                            $ohlc->setOpen($message['open']);
                            $ohlc->setClose($message['close']);
                            $ohlc->setHigh($message['high']);
                            $ohlc->setLow($message['low']);
                            $ohlc->setDateAdded($timeNow);

                            return $ohlc;
                        }
                    }
                }
            }
        }

        return false;
    }

    private function getDateTime($timeNow = null)
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