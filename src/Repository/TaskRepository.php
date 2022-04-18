<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Task $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Task $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllWithProject(string $projectId) : array
    {
        return $this->createQueryBuilder('t')
                        ->select('t')
                        ->innerJoin(
                            't.project', 'p',
                            Join::WITH, "t.project = p.id AND p.id = :id"
                            )
                        ->setParameter('id', $projectId)
                        ->addSelect('p')
                        ->getQuery()
                        // ->getSQL();
                        ->getResult();
    }

    public function findWithProject(string $projectId, string $id) : ?Task
    {
        return $this->createQueryBuilder('t')
                    ->innerJoin('t.project', 'p', Join::WITH,  'p.id = ?1')
                    ->addSelect('p')
                    ->andWhere('t.id = ?2')
                    ->setParameters(
                        new ArrayCollection(
                            [
                                new Parameter('1', $projectId),
                                new Parameter('2', $id)
                            ]
                        )
                    )
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    // public function findByProjectId($value)
    // {
    //     return $this->createQueryBuilder('t')
    //         ->select('t.id, t.name, t.deletedAt')
    //         ->addSelect('prj.id AS project_id, prj.title AS project_title, prj.description AS project_description,
    //                     prj.status AS project_status, prj.duration AS project_duration, prj.client AS project_client,
    //                     prj.company AS project_company, prj.deletedAt as project_deletedAt'
    //         )
    //         ->leftJoin(
    //             Project::class, 'prj',
    //             Join::WITH, "prj.id = t.project AND t.project = :val"
    //             )
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getResult();
    // }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
