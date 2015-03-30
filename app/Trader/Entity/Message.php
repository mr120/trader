<?php

namespace Trader\Entity;

/**
 * @Entity(repositoryClass="Trader\Repository\Message")
 * @Table(name="message")
 */
class Message
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /**
     * @Column(type="string")
     */
    private $userId;
    /**
     * @Column(type="string")
     */
    private $currencyFrom;
    /**
     * @Column(type="string")
     */
    private $currencyTo;
    /**
     * @Column(type="float")
     */
    private $amountSell;
    /**
     * @Column(type="float")
     */
    private $amountBuy;
    /**
     * @Column(type="float")
     */
    private $rate;
    /**
     * @Column(type="datetime")
     */
    private $timePlaced;
    /**
     * @Column(type="string")
     */
    private $originatingCountry;

    public function getId() { return $this->id; }

    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }

    public function getCurrencyFrom() { return $this->currencyFrom; }
    public function setCurrencyFrom($currencyFrom) { $this->currencyFrom = $currencyFrom; }

    public function getCurrencyTo() { return $this->currencyTo; }
    public function setCurrencyTo($currencyTo) { $this->currencyTo = $currencyTo; }

    public function getAmountSell() { return $this->amountSell; }
    public function setAmountSell($amountSell) { $this->amountSell = $amountSell; }

    public function getAmountBuy() { return $this->amountBuy; }
    public function setAmountBuy($amountBuy) { $this->amountBuy = $amountBuy; }

    public function getRate() { return $this->rate; }
    public function setRate($rate) { $this->rate = $rate; }

    public function getTimePlaced() { return $this->timePlaced; }
    public function setTimePlaced($timePlaced) { $this->timePlaced = $timePlaced; }

    public function getOriginatingCountry() { return $this->originatingCountry; }
    public function setOriginatingCountry($originatingCountry) { $this->originatingCountry = $originatingCountry; }
}