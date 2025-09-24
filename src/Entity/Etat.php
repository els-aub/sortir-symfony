<?php
namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
#[ORM\Table(name: 'etats')]
class Etat
{
    #[ORM\Id]
    #[ORM\Column(name: 'no_etat', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO
    private int $noEtat;

    #[ORM\Column(name: 'libelle', type: 'string', length: 30)]
    private string $libelle;

    public function getNoEtat(): int { return $this->noEtat; }
    public function setNoEtat(int $v): self { $this->noEtat = $v; return $this; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $v): self { $this->libelle = $v; return $this; }

    public function __toString(): string { return $this->libelle; }
}
