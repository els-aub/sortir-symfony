<?php
namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[ORM\Table(name: 'villes')]
class Ville
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_ville', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO
    private int $idVille;

    #[ORM\Column(name: 'nom_ville', type: 'string', length: 30)]
    private string $nomVille;

    #[ORM\Column(name: 'code_postal', type: 'string', length: 10)]
    private string $codePostal;

    public function getIdVille(): int { return $this->idVille; }
    public function setIdVille(int $v): self { $this->idVille = $v; return $this; }

    public function getNomVille(): string { return $this->nomVille; }
    public function setNomVille(string $v): self { $this->nomVille = $v; return $this; }

    public function getCodePostal(): string { return $this->codePostal; }
    public function setCodePostal(string $v): self { $this->codePostal = $v; return $this; }

    public function __toString(): string
    {
        return $this->nomVille.' ('.$this->codePostal.')';
    }


}
