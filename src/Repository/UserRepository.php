<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\Exception\CantSaveUser;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager|\Doctrine\ORM\EntityManagerInterface|\Doctrine\ORM\Mapping\ClassMetadata
     */
    private $entityManager;

    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $this->getEntityManager();
    }

    /**
     * @param User $user
     *
     * @throws CantSaveUser
     */
    public function save(User $user): void {
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ORMException | DBALException | PDOException $exception) {
            throw new CantSaveUser("Не удалось сохранить пользователя");
        }
    }
}
