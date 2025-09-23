<?php
namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
#[ORM\Table(name: 'SITES')]
class Site
{
    #[ORM\Id]
    #[ORM\Column(name: 'no_site', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO
    private int $noSite;

    #[ORM\Column(name: 'nom_site', type: 'string', length: 30)]
    private string $nomSite;

    public function getNoSite(): int { return $this->noSite; }
    public function setNoSite(int $noSite): self { $this->noSite = $noSite; return $this; }

    public function getNomSite(): string { return $this->nomSite; }
    public function setNomSite(string $nomSite): self { $this->nomSite = $nomSite; return $this; }

    public function __toString(): string { return $this->nomSite; }
}
