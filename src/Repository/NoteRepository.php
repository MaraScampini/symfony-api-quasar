<?php

namespace App\Repository;

use App\Entity\Note;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    // Custom method to get past notes
    public function getPastNotes()
    {
        $em = $this->getEntityManager();


        $today = new DateTime('now');
        $past = $today->sub(new DateInterval('P7D'));

        $query = $em->createQuery(
            'SELECT n
            FROM App\Entity\Note n
            WHERE n.date < :date'
        )->setParameter('date', $past);

        return $query->getResult();
    }
}
