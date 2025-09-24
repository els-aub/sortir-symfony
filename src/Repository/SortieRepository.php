<?php
namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[]
     * $filters keys: site(int id), q(string), organisateur(bool), inscrit(bool), passees(bool)
     * $current peut être null (si pas encore d’auth)
     */
    public function findByFilters(array $filters, ?Participant $current = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.lieu', 'l')->addSelect('l')
            ->leftJoin('s.etat', 'e')->addSelect('e')
            ->leftJoin('s.organisateur', 'o')->addSelect('o')
            ->leftJoin('o.site', 'si')->addSelect('si')
            ->leftJoin('s.inscriptions', 'i')->addSelect('i')
            ->orderBy('s.dateDebut', 'ASC')
            ->groupBy('s.noSortie'); // évite les doublons quand plusieurs inscriptions

        // Filtre Site (select)
        if (isset($filters['site']) && $filters['site'] !== '' && $filters['site'] !== null) {
            $qb->andWhere('si.noSite = :siteId')->setParameter('siteId', (int)$filters['site']);
        }

        // Filtre texte (nom contient)
        if (!empty($filters['q'])) {
            $qb->andWhere('s.nom LIKE :q')->setParameter('q', '%'.$filters['q'].'%');
        }

        // Je suis organisateur
        if (!empty($filters['organisateur']) && $current) {
            $qb->andWhere('o = :current')->setParameter('current', $current);
        }

        // Je suis inscrit
        if (!empty($filters['inscrit']) && $current) {
            $qb->andWhere('i.participant = :currentInscrit')->setParameter('currentInscrit', $current);
        }

        // Passées / A venir (par défaut, on n’affiche que les à venir)
        $now = new \DateTimeImmutable();
        if (!empty($filters['passees'])) {
            $qb->andWhere('s.dateDebut < :now');
        } else {
            $qb->andWhere('s.dateDebut >= :now');
        }
        $qb->setParameter('now', $now);

        return $qb->getQuery()->getResult();
    }
}
