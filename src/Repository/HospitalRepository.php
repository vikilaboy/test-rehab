<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\Hospital;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function findAllPaginated(int $page = 1)
    {
        $qb = $this->createQueryBuilder('h')
            ->select('h')
            ->orderBy('h.id', 'DESC');

        return $this->createPaginator($qb, $page);
    }

    /**
     * @param int $page
     * @return array
     */
    public function findWithMessages(int $page = 1)
    {
        $qb = $this->createQueryBuilder('h')
            ->select(['h', 'COUNT(c.id) AS messages'])
            ->leftJoin(Contact::class, 'c', Join::WITH, 'h.id = c.hospital')
            ->groupBy('h.id')
            ->orderBy('h.id', 'DESC');

        return $this->createPaginator($qb, $page);
    }
}
