<?php

namespace EspritEntreAide\EvenementBundle\Repository;
use Symfony\Component\Validator\Constraints\Date;

/**
 * EvenementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EvenementRepository extends \Doctrine\ORM\EntityRepository
{
    public function trier(){
        return $this->findBy(array('etat'=>0), array('dateE'=>'ASC'));
    }


        public function findE()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.dateE', 'DESC')

            ->getQuery()
            ->setMaxResults(5)

            ->getResult();




    }


    public function countEvent()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')->where("a.etat = 0") ->andWhere("a.dateE > :date")
            ->setParameter('date',new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function rechercherEvenement($search)
    {


        $q= $this->createQueryBuilder('o')
            ->where("o.etat = 0")
            ->andWhere("o.dateE > :date")
            ->andWhere("o.descE LIKE  :search OR o.titreE LIKE  :search OR o.dateE LIKE  :search OR o.typeE LIKE  :search ")
            ->setParameter(':search','%'.$search.'%')
            ->setParameter('date',new \DateTime())
        ;

        return $q->getQuery()->getResult();
    }

    public function afficherFront(){
        return $this->createQueryBuilder('o')
            ->where("o.etat = 0")
            ->andWhere("o.dateE > :date")
            ->setParameter('date',new \DateTime())
            ->getQuery()
            ->getResult()
            ;
    }

    public function affichereventspasserFront(){
        return $this->createQueryBuilder('e')
            ->Where("e.dateE < :date")
            ->setParameter('date',new \DateTime())
            ->getQuery()
            ->getResult()
            ;
    }
    public function countEvents()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')->where("a.etat = 0") ->andWhere("a.dateE > :date")
            ->setParameter('date',new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();
    }
}

