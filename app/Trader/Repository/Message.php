<?php

namespace Trader\Repository;


use Doctrine\ORM\EntityRepository;

class Message extends EntityRepository {

    public function getData()
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('m')
            ->from('\Trader\Entity\Message', 'm');

        $return = $qb->getQuery()->getArrayResult();

        return $return;

    }

    public function getLatestData()
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('m')
            ->from('\Trader\Entity\Message', 'm')
            ->orderBy('m.timePlaced', 'DESC')
            ->groupBy('m.currencyFrom, m.currencyTo');

        $return = $qb->getQuery()->getArrayResult();

        return $return;

    }

    public function getDataByPairOverTime($from, $to, \DateTime $time)
    {
        $time = clone($time);
        $timeFrom = $time->format('Y-m-d H:i:s');
        $timeTo = $time->modify('+15 minutes')->format('Y-m-d H:i:s');

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('m.timePlaced, m.rate, m.amountBuy')
            ->from('\Trader\Entity\Message', 'm')
            ->where('m.currencyFrom = :from')
            ->andWhere('m.currencyTo = :to')
            ->andWhere('(m.timePlaced BETWEEN :timeFrom AND :timeTo)')
            ->orderBy('m.timePlaced')
            ->setParameters(
                array(
                    'from'=>strtoupper($from),
                    'to'=>strtoupper($to),
                    'timeFrom'=>$timeFrom,
                    'timeTo'=>$timeTo,
                )
            );

        $qb2 = $em->createQueryBuilder();
        $qb2->select('max(m.amountBuy) as high, min(m.amountBuy) as low')
            ->from('\Trader\Entity\Message', 'm')
            ->where('m.currencyFrom = :from')
            ->andWhere('m.currencyTo = :to')
            ->andWhere('(m.timePlaced BETWEEN :timeFrom AND :timeTo)')
            ->orderBy('m.timePlaced')
            ->setParameters(
                array(
                    'from'=>strtoupper($from),
                    'to'=>strtoupper($to),
                    'timeFrom'=>$timeFrom,
                    'timeTo'=>$timeTo,
                )
            );

        $spread = $qb->getQuery()->getArrayResult();
        $minMax = $qb2->getQuery()->getArrayResult();

        if(is_array($minMax)){
            if(!is_null($minMax[0]['high'])) {

                $first = reset($spread);
                $last = end($spread);

                $array = [
                    'high' => $minMax[0]['high'],
                    'low' => $minMax[0]['low'],
                    'open' => $first['amountBuy'],
                    'close' => $last['amountBuy']
                ];

                return $array;
            }
        }

        return array();

    }

    public function getDataByPair($from, $to)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('m.id, m.timePlaced, m.rate, m.amountBuy, m.amountSell, m.timePlaced')
           ->from('\Trader\Entity\Message', 'm')
            ->where('m.currencyFrom = :from')
            ->andWhere('m.currencyTo = :to')
            ->orderBy('m.timePlaced', 'DESC')
            ->setParameters(
                array(
                    'from'=>strtoupper($from),
                    'to'=>strtoupper($to)
                )
            );

        return $qb->getQuery()->getArrayResult();
    }

} 