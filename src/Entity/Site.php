<?php
namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
#[ORM\Table(name: 'sites')]
class Site
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_site', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO_INCREMENT en base
    private int $idSite;

    #[ORM\Column(name: 'nom_site', type: 'string', length: 30)]
    private string $nomSite;

    public function getIdSite(): int { return $this->idSite; }
    public function setIdSite(int $v): self { $this->idSite = $v; return $this; }

    public function getNomSite(): string { return $this->nomSite; }
    public function setNomSite(string $v): self { $this->nomSite = $v; return $this; }

    public function __toString(): string { return $this->nomSite; }
}
