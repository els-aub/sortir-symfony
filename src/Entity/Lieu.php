<?php
namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
#[ORM\Table(name: 'lieux')]
class Lieu
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_lieu', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO_INCREMENT
    private int $idLieu;

    #[ORM\Column(name: 'nom_lieu', type: 'string', length: 30)]
    private string $nomLieu; // genre "Parc" etc

    #[ORM\Column(name: 'rue', type: 'string', length: 30, nullable: true)]
    private ?string $rue = null; // peut etre vide

    #[ORM\Column(name: 'latitude', type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(name: 'longitude', type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\ManyToOne(targetEntity: Ville::class)]
    #[ORM\JoinColumn(name: 'villes_id_ville', referencedColumnName: 'id_ville', nullable: false)]
    private Ville $ville;

    public function getIdLieu(): int { return $this->idLieu; }
    public function setIdLieu(int $v): self { $this->idLieu = $v; return $this; }

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

    // version test
    /*
    public function getCoord(): string {
        return $this->latitude . ',' . $this->longitude; // à faire/!\
    }
    */
}
