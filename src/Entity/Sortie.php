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
    #[ORM\Column(name: 'id_sortie', type: 'integer')]
    #[ORM\GeneratedValue]
    private int $idSortie;

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

    // === Nouveau champ : motif annulation ===
    #[ORM\Column(name: 'motif_annulation', type: 'string', length: 255, nullable: true)]
    private ?string $motifAnnulation = null;
    // nullable=true car à la création il n’y a pas de motif
    // mais dans le form d’annulation ce sera required

    #[ORM\ManyToOne(targetEntity: Participant::class)]
    #[ORM\JoinColumn(name: 'id_participant', referencedColumnName: 'id_participant', nullable: false)]
    private Participant $organisateur;

    #[ORM\ManyToOne(targetEntity: Lieu::class)]
    #[ORM\JoinColumn(name: 'id_lieu', referencedColumnName: 'id_lieu', nullable: false)]
    private Lieu $lieu;

    #[ORM\ManyToOne(targetEntity: Etat::class)]
    #[ORM\JoinColumn(name: 'id_etat', referencedColumnName: 'id_etat', nullable: false)]
    private Etat $etat;

    // === Relation ManyToMany avec Participant via table "inscriptions" ===
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'sortiesInscrites')]
    #[ORM\JoinTable(
        name: 'inscriptions',
        joinColumns: [new ORM\JoinColumn(name: 'sorties_id_sortie', referencedColumnName: 'id_sortie')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'participants_id_participant', referencedColumnName: 'id_participant')]
    )]
    private Collection $participants; // Liste des participants inscrits

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    // ---- participants inscrits ----
    /** @return Collection<int, Participant> */
    public function getParticipants(): Collection { return $this->participants; }

    public function addParticipant(Participant $p): self
    {
        if (!$this->participants->contains($p)) {
            $this->participants->add($p);
            $p->getSortiesInscrites()->add($this);
        }
        return $this;
    }

    public function removeParticipant(Participant $p): self
    {
        if ($this->participants->removeElement($p)) {
            $p->getSortiesInscrites()->removeElement($this);
        }
        return $this;
    }

    // ---- getters/setters ----
    public function getIdSortie(): int { return $this->idSortie; }

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

    // === getters/setters motifAnnulation ===
    public function getMotifAnnulation(): ?string { return $this->motifAnnulation; }
    public function setMotifAnnulation(?string $v): self { $this->motifAnnulation = $v; return $this; }
}
