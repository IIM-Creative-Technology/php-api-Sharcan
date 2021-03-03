<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 */
class Etudiant
{
    /**
     * @Groups({"etudiant"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"etudiant"})
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups({"etudiant"})
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @Groups({"etudiant"})
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @Groups({"etudiant"})
     * @ORM\Column(type="date")
     */
    private $annee;

    /**
     * @Groups({"classe_information"})
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $promotion;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getAnnee(): ?\DateTimeInterface
    {
        return $this->annee;
    }

    public function setAnnee(\DateTimeInterface $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getPromotion(): ?Classe
    {
        return $this->promotion;
    }

    public function setPromotion(?Classe $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }
}
