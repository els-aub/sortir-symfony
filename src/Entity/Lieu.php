<?php
namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: LieuRepository::class)]
#[ORM\Table(name: 'LIEUX')]
class Lieu
{
    #[ORM\Id]
    #[ORM\Column(name: 'no_lieu', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO
    private int $noLieu;

    #[ORM\Column(name: 'nom_lieu', type: 'string', length: 30)]
    private string $nomLieu;

    #[ORM\Column(name: 'rue', type: 'string', length: 30, nullable: true)]
    private ?string $rue = null;

    #[ORM\Column(name: 'latitude', type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(name: 'longitude', type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\ManyToOne(targetEntity: Ville::class)]
    #[ORM\JoinColumn(name: 'villes_no_ville', referencedColumnName: 'no_ville', nullable: false)]
    private Ville $ville;

    public function getNoLieu(): int { return $this->noLieu; }
    public function setNoLieu(int $v): self { $this->noLieu = $v; return $this; }

    public function getNomLieu(): string { return $this->nomLieu; }
    public function setNomLieu(string $v): self { $this->nomLieu = $v; return $this; }

    public function getRue(): ?string { return $this->rue; }
    public function setRue(?string $v): self { $this->rue = $v; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $v): self { $this->latitude = $v; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $v): self { $this->longitude = $v; return $this; }

    public function getVille(): Ville { return $this->ville; }
    public function setVille(Ville $v): self { $this->ville = $v; return $this; }

    public function __toString(): string { return $this->nomLieu; }
}
