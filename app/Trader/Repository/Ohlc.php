<?php

namespace Trader\Repository;


use Doctrine\ORM\EntityRepository;

class Ohlc extends EntityRepository {

    public function getDataByPair($from, $to)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('o')
            ->from('\Trader\Entity\Ohlc', 'o')
            ->where('o.currencyFrom = :from')
            ->andWhere('o.currencyTo = :to')
            ->orderBy('o.dateAdded')
            ->setParameters(
                array(
                    'from'=>strtoupper($from),
                    'to'=>strtoupper($to)
                )
            );

        $return = $qb->getQuery()->getArrayResult();

        return $return;

    }

    public function getLatestDataByPair($from, $to)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('o.dateAdded')
            ->from('\Trader\Entity\Ohlc', 'o')
            ->where('o.currencyFrom = :from')
            ->andWhere('o.currencyTo = :to')
            ->orderBy('o.dateAdded', 'DESC')
            ->setParameters(
                array(
                    'from'=>strtoupper($from),
                    'to'=>strtoupper($to)
                )
            )
            ->setMaxResults(1);


        $return = $qb->getQuery()->getArrayResult();

        if(!empty($return)){
            return $return[0];
        }
        return array();
    }

} 