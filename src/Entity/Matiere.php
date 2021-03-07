<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MatiereRepository::class)
 */
class Matiere
{
    /**
     * @Groups({"matiere"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"matiere"})
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups({"matiere"})
     * @ORM\Column(type="date")
     */
    private $debut_date;

    /**
     * @Groups({"matiere"})
     * @ORM\Column(type="date")
     */
    private $fin_date;

    /**
     * @Groups({"matiere_intervenant"})
     * @ORM\ManyToOne(targetEntity=Intervenant::class, inversedBy="matieres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $intervenant;

    /**
     * @Groups({"matiere_classe"})
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="matieres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

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
}
