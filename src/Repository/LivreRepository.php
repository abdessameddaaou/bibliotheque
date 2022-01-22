<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Match_;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    // /**
    //  * @return Livre[] Returns an array of Livre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
// un id te3 livre qui a nombre de plus page 50 et titre commence par lettre A.
  /*  public function affichez()
    {
        return $this->createQueryBuilder('a')
        ->where('a.nbpages = 5')
        ->getQuery()
        ->getResult();

    }*/

    public function listerlivresperiodes($date1, $date2)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->where('s.date_de_parution BETWEEN :date1 AND :date2')
            ->setParameter('date1', $date1->format('Y-m-d'))
            ->setParameter('date2', $date2->format('Y-m-d'));

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function listerlivresperiodesnotes($date1, $date2, $note1, $note2)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->where('s.date_de_parution BETWEEN :date1 AND :date2')
            ->andWhere('s.note BETWEEN :note1 AND :note2')
            ->setParameter('date1', $date1->format('Y-m-d'))
            ->setParameter('date2', $date2->format('Y-m-d'))
            ->setParameter('note1', $note1)
            ->setParameter('note2', $note2);

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function chercherlivretitre($motcle)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->where('s.titre LIKE :motcle')
        ->setParameter('motcle', $motcle);

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
  
}
