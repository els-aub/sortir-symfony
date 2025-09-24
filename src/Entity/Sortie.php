<?php
namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
#[ORM\Table(name: 'sorties')]
class Sortie
{
    #[ORM\Id]
    #[ORM\Column(name: 'no_sortie', type: 'integer')]
    #[ORM\GeneratedValue] // si ta PK n'est pas AUTO en base, remplace par: #[ORM\GeneratedValue(strategy: 'NONE')]
    private int $noSortie;

    #[ORM\Column(name: 'nom', type: 'string', length: 30)]
    private string $nom;

    #[ORM\Column(name: 'datedebut', type: 'datetime')]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(name: 'duree', type: 'integer', nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(name: 'datecloture', type: 'datetime')]
    private \DateTimeInterface $dateCloture;

    #[ORM\Column(name: 'nbinscriptionsmax', type: 'integer')]
    private int $nbInscriptionsMax;

    #[ORM\Column(name: 'descriptioninfos', type: 'string', length: 500, nullable: true)]
    private ?string $descriptionInfos = null;

    #[ORM\Column(name: 'urlPhoto', type: 'string', length: 250, nullable: true)]
    private ?string $urlPhoto = null;

    #[ORM\ManyToOne(targetEntity: Participant::class)]
    #[ORM\JoinColumn(name: 'organisateur', referencedColumnName: 'no_participant', nullable: false)]
    private Participant $organisateur;

    #[ORM\ManyToOne(targetEntity: Lieu::class)]
    #[ORM\JoinColumn(name: 'lieux_no_lieu', referencedColumnName: 'no_lieu', nullable: false)]
    private Lieu $lieu;

    #[ORM\ManyToOne(targetEntity: Etat::class)]
    #[ORM\JoinColumn(name: 'etats_no_etat', referencedColumnName: 'no_etat', nullable: false)]
    private Etat $etat;

    #[ORM\OneToMany(mappedBy: 'sortie', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    // ---- inscriptions helpers ----
    /** @return Collection<int, Inscription> */
    public function getInscriptions(): Collection { return $this->inscriptions; }

    public function addInscription(Inscription $i): self
    {
        if (!$this->inscriptions->contains($i)) {
            $this->inscriptions->add($i);
            $i->setSortie($this);
        }
        return $this;
    }

    public function removeInscription(Inscription $i): self
    {
        if ($this->inscriptions->removeElement($i)) {
            if ($i->getSortie() === $this) {
                // relation owning side non-nullable en base : on ne met pas null
                $i->setSortie($this);
            }
        }
        return $this;
    }

    // ---- getters/setters ----
    public function getNoSortie(): int { return $this->noSortie; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $v): self { $this->nom = $v; return $this; }

    public function getDateDebut(): \DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $v): self { $this->dateDebut = $v; return $this; }

    public function getDuree(): ?int { return $this->duree; }
    public function setDuree(?int $v): self { $this->duree = $v; return $this; }

    public function getDateCloture(): \DateTimeInterface { return $this->dateCloture; }
    public function setDateCloture(\DateTimeInterface $v): self { $this->dateCloture = $v; return $this; }

    public function getNbInscriptionsMax(): int { return $this->nbInscriptionsMax; }
    public function setNbInscriptionsMax(int $v): self { $this->nbInscriptionsMax = $v; return $this; }

    public function getDescriptionInfos(): ?string { return $this->descriptionInfos; }
    public function setDescriptionInfos(?string $v): self { $this->descriptionInfos = $v; return $this; }

    public function getUrlPhoto(): ?string { return $this->urlPhoto; }
    public function setUrlPhoto(?string $v): self { $this->urlPhoto = $v; return $this; }

    public function getOrganisateur(): Participant { return $this->organisateur; }
    public function setOrganisateur(Participant $v): self { $this->organisateur = $v; return $this; }

    public function getLieu(): Lieu { return $this->lieu; }
    public function setLieu(Lieu $v): self { $this->lieu = $v; return $this; }

    public function getEtat(): Etat { return $this->etat; }
    public function setEtat(Etat $v): self { $this->etat = $v; return $this; }
}
