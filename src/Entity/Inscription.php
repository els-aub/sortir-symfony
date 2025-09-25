<?php
namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[ORM\Table(name: 'inscriptions')]
class Inscription
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Sortie::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(name: 'sorties_id_sortie', referencedColumnName: 'id_sortie', nullable: false)]
    private Sortie $sortie;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Participant::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(name: 'participants_id_participant', referencedColumnName: 'id_participant', nullable: false)]
    private Participant $participant;

    #[ORM\Column(name: 'date_inscription', type: 'datetime')]
    private \DateTimeInterface $dateInscription;

    public function __construct()
    {
        $this->dateInscription = new \DateTimeImmutable();
    }

    public function getSortie(): Sortie { return $this->sortie; }
    public function setSortie(Sortie $s): self { $this->sortie = $s; return $this; }

    public function getParticipant(): Participant { return $this->participant; }
    public function setParticipant(Participant $p): self { $this->participant = $p; return $this; }

    public function getDateInscription(): \DateTimeInterface { return $this->dateInscription; }
    public function setDateInscription(\DateTimeInterface $d): self { $this->dateInscription = $d; return $this; }
}
