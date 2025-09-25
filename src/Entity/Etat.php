<?php
namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
#[ORM\Table(name: 'etats')]   // <-- pluriel pour cohérence avec les autres
class Etat
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_etat', type: 'integer')] // <-- snake_case
    #[ORM\GeneratedValue] // AUTO_INCREMENT
    private int $idEtat;

    #[ORM\Column(name: 'libelle', type: 'string', length: 30)]
    private string $libelle;

    // ---- getters / setters ----
    public function getIdEtat(): int
    {
        return $this->idEtat;
    }

    public function setIdEtat(int $v): self
    {
        $this->idEtat = $v;
        return $this;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $v): self
    {
        $this->libelle = $v;
        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle;
    }
}
