<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MatiereRepository::class)
 */
class Matiere
{
    /**
     * @Serializer\Groups({"matiere"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Serializer\Groups({"matiere"})
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Serializer\Groups({"matiere"})
     * @ORM\Column(type="date")
     */
    private $debut_date;

    /**
     * @Serializer\Groups({"matiere"})
     * @ORM\Column(type="date")
     */
    private $fin_date;

    /**
     * @Serializer\Groups({"matiere_intervenant"})
     * @ORM\ManyToOne(targetEntity=Intervenant::class, inversedBy="matieres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $intervenant;

    /**
     * @Serializer\Groups({"matiere_classe"})
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="matieres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

    /**
     * @Groups({"matiere_intervenant"})
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="matiere", orphanRemoval=true)
     */
    private $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDebutDate(): ?\DateTimeInterface
    {
        return $this->debut_date;
    }

    public function setDebutDate(\DateTimeInterface $debut_date): self
    {
        $this->debut_date = $debut_date;

        return $this;
    }

    public function getFinDate(): ?\DateTimeInterface
    {
        return $this->fin_date;
    }

    public function setFinDate(\DateTimeInterface $fin_date): self
    {
        $this->fin_date = $fin_date;

        return $this;
    }

    public function getIntervenant(): ?Intervenant
    {
        return $this->intervenant;
    }

    public function setIntervenant(?Intervenant $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setMatiere($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getMatiere() === $this) {
                $note->setMatiere(null);
            }
        }

        return $this;
    }
}
