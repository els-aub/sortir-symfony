<?php
namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[ORM\Table(name: 'INSCRIPTIONS')]
class Inscription
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Sortie::class)]
    #[ORM\JoinColumn(name: 'sorties_no_sortie', referencedColumnName: 'no_sortie', nullable: false)]
    private Sortie $sortie;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Participant::class)]
    #[ORM\JoinColumn(name: 'participants_no_participant', referencedColumnName: 'no_participant', nullable: false)]
    private Participant $participant;

    #[ORM\Column(name: 'date_inscription', type: 'datetime')]
    private \DateTimeInterface $dateInscription;

    public function getSortie(): Sortie { return $this->sortie; }
    public function setSortie(Sortie $v): self { $this->sortie = $v; return $this; }

    public function getParticipant(): Participant { return $this->participant; }
    public function setParticipant(Participant $v): self { $this->participant = $v; return $this; }

    public function getDateInscription(): \DateTimeInterface { return $this->dateInscription; }
    public function setDateInscription(\DateTimeInterface $v): self { $this->dateInscription = $v; return $this; }
}
