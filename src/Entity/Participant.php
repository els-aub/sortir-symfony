<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\Table(name: 'PARTICIPANTS')]
#[ORM\UniqueConstraint(name: 'participants_pseudo_uk', columns: ['pseudo'])]
class Participant
{
    #[ORM\Id]
    #[ORM\Column(name: 'no_participant', type: 'integer')]
    #[ORM\GeneratedValue] // AUTO
    private int $noParticipant;

    #[ORM\Column(name: 'pseudo', type: 'string', length: 30)]
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
    private bool $administrateur;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif;

    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(name: 'sites_no_site', referencedColumnName: 'no_site', nullable: false)]
    private Site $site;

    public function getNoParticipant(): int { return $this->noParticipant; }
    public function setNoParticipant(int $id): self { $this->noParticipant = $id; return $this; }

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

    public function __toString(): string
    {
        return sprintf('%s %s (%s)', $this->prenom ?? '', $this->nom ?? '', $this->pseudo ?? '');
    }
}
