<?php

namespace Trader\Entity;

/**
 * @Entity(repositoryClass="Trader\Repository\Ohlc")
 * @Table(name="ohlc")
 */
class Ohlc
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
    private $currencyFrom;
    /**
     * @Column(type="string")
     */
    private $currencyTo;
    /**
     * @Column(type="float")
     */
    private $high;
    /**
     * @Column(type="float")
     */
    private $low;
    /**
     * @Column(type="float")
     */
    private $open;
    /**
     * @Column(type="float")
     */
    private $close;
    /**
     * @Column(type="datetime")
     */
    private $dateAdded;

    public function getId() { return $this->id; }

    public function getCurrencyFrom() { return $this->currencyFrom; }
    public function setCurrencyFrom($currencyFrom) { $this->currencyFrom = $currencyFrom; }

    public function getCurrencyTo() { return $this->currencyTo; }
    public function setCurrencyTo($currencyTo) { $this->currencyTo = $currencyTo; }

    public function getHigh() { return $this->high; }
    public function setHigh($high) { $this->high = $high; }

    public function getLow() { return $this->low; }
    public function setLow($low) { $this->low = $low; }

    public function getOpen() { return $this->open; }
    public function setOpen($open) { $this->open = $open; }

    public function getClose() { return $this->close; }
    public function setClose($close) { $this->close = $close; }

    public function getDateAdded() { return $this->dateAdded; }
    public function setDateAdded($dateAdded) { $this->dateAdded = $dateAdded; }
}