<?php
namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\Table(name: 'participants')]
class Participant
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_participant', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO_INCREMENT en base
    private int $idParticipant;

    #[ORM\Column(name: 'pseudo', type: 'string', length: 30, unique: true)]
    private string $pseudo;

    #[ORM\Column(name: 'nom', type: 'string', length: 30)]
    private string $nom;

    #[ORM\Column(name: 'prenom', type: 'string', length: 30)]
    private string $prenom;

    #[ORM\Column(name: 'telephone', type: 'string', length: 15, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: 'mail', type: 'string', length: 20)]
    private string $mail;

    #[ORM\Column(name: 'mot_de_passe', type: 'string', length: 20)]
    private string $motDePasse;

    #[ORM\Column(name: 'administrateur', type: 'boolean')]
    private bool $administrateur = false;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(name: 'sites_id_site', referencedColumnName: 'id_site', nullable: false)]
    private Site $site;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    // --------- Relation avec User ---------
    #[ORM\OneToOne(inversedBy: 'participant', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    // ---- getters / setters ----
    public function getIdParticipant(): int { return $this->idParticipant; }
    public function setIdParticipant(int $v): self { $this->idParticipant = $v; return $this; }

    public function getPseudo(): string { return $this->pseudo; }
    public function setPseudo(string $v): self { $this->pseudo = $v; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $v): self { $this->nom = $v; return $this; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $v): self { $this->prenom = $v; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $v): self { $this->telephone = $v; return $this; }

    public function getMail(): string { return $this->mail; }
    public function setMail(string $v): self { $this->mail = $v; return $this; }

    public function getMotDePasse(): string { return $this->motDePasse; }
    public function setMotDePasse(string $v): self { $this->motDePasse = $v; return $this; }

    public function isAdministrateur(): bool { return $this->administrateur; }
    public function setAdministrateur(bool $v): self { $this->administrateur = $v; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $v): self { $this->actif = $v; return $this; }

    public function getSite(): Site { return $this->site; }
    public function setSite(Site $site): self { $this->site = $site; return $this; }

    /** @return Collection<int, Inscription> */
    public function getInscriptions(): Collection { return $this->inscriptions; }

    public function addInscription(Inscription $i): self
    {
        if (!$this->inscriptions->contains($i)) {
            $this->inscriptions->add($i);
            $i->setParticipant($this);
        }
        return $this;
    }

    public function removeInscription(Inscription $i): self
    {
        if ($this->inscriptions->removeElement($i)) {
            if ($i->getParticipant() === $this) {
                $i->setParticipant($this);
            }
        }
        return $this;
    }

    // ---- Relation avec User ----
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        // Assurer la cohérence côté User
        if ($user->getParticipant() !== $this) {
            $user->setParticipant($this);
        }

        return $this;
    }
}
