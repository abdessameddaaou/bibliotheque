<?php

namespace App\Repository;

use App\Entity\Auteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Auteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Auteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Auteur[]    findAll()
 * @method Auteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auteur::class);
    }

    public function findAuteur(){
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->select('a.ecrire')->where('a.sexe = :val')->setParameter('val',1)
                    ;
        return $queryBuilder->getQuery()->getResult();
    }
    
    public function auteurmin()
    {
        return $this->createQueryBuilder('a')
        ->innerJoin('a.titre', 'A')
        ->select('count(a.titre)')
        ->getQuery()
        ->getResult();

    }

    //Action 20 
    public function findAuteursGenres(){
        //$queryBuilder = $this->createQueryBuilder('s');
        //$queryBuilder->where('s.id = :id')
        //              ->setParameter('id' , 58 ) ;
        //return $queryBuilder->getQuery()->getResult() ; 

        // $query = $this->getEntityManager() 
        //               ->createQuery("SELECT genre.nom
        //                              FROM App\Entity\Genre AS genre
        //                              WHERE genre.id 
        //                              IN (
        //                                  SELECT livre.id 
        //                                  FROM App\Entity\Livre AS livre
        //                                  WHERE livre.id IN (
        //                                      SELECT auteur.id 
        //                                      FROM App\Entity\Auteur AS auteur
        //                                      WHERE auteur.id = 61
        //                                      )
        //                              )"
        //                              ); 
        // $query = $this->getEntityManager()
        // ->createQuery("SELECT g
        //      FROM App\Entity\Genre g WHERE g.id IN (SELECT l.id FROM App\Entity\Livre l WHERE l.id IN (SELECT a.id FROM App\Entity\Auteur a WHERE a.id = '6') ORDER BY l.date_de_parution)");
       $query=  $this->createQueryBuilder("a")
                ->select('a.nom_prenom','g.nom')
                ->join('a.ecrire','l','a.id = l.auteur_id')
                ->join('l.genres','g','l.id = g.livre_id')
                //->groupBy('a.nom_prenom')
                ->distinct()
                ->orderBy('l.date_de_parution','asc')
                ;
               
            dd($result = $query->getQuery()->getResult());
            //dd(array_map('current',$result));
            //dd($result);
        return $result; 
    }

   
}
