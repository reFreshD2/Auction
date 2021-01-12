<?php

namespace App\Repository;

use App\Entity\Product;
use App\Repository\Exception\CantSaveProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager|\Doctrine\ORM\EntityManagerInterface|\Doctrine\ORM\Mapping\ClassMetadata
     */
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
        $this->entityManager = $this->getEntityManager();
    }

    public function save(Product $product) {
        try {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (ORMException | DBALException | PDOException $exception) {
            throw new CantSaveProduct($exception->getMessage());
        }
    }
}
