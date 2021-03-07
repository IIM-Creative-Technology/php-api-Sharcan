<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Intervenant;
use App\Entity\Matiere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i=1; $i<6; $i++) {
            $classe = new Classe();
            $classe->setName('Promotion n° ' . $i);
            $classe->setAnnee(new \DateTime());

            $manager->persist($classe);

            $this->setEtudiants($classe, $manager, $faker);

        }

        $this->setIntervenants($manager, $faker);
        $this->setMatieres($manager, $faker);

        $manager->flush();

    }


    private function setSingleClasse(ObjectManager $manager, $faker)
    {
        $classe = new Classe();
        $classe->setName('Promotion n° ' . random_int(30, 999));
        $classe->setAnnee(new \DateTime());

        $manager->persist($classe);

        return $classe;
    }

    private function setEtudiants(Classe $classe, ObjectManager $manager, $faker)
    {
        for($i= 0; $i <= 30; $i++) {
            $etudiant = new Etudiant();

            $etudiant->setNom($faker->name);
            $etudiant->setPrenom($faker->firstName);
            $etudiant->setAnnee(new \DateTime(random_int(2020, 2025)));
            $etudiant->setAge(random_int(15, 99));
            $etudiant->setPromotion($classe);

            $manager->persist($etudiant);

        }
    }

    private function setIntervenants(ObjectManager $manager, $faker)
    {

        for($i = 1; $i <= 20; $i++) {
            $intervenant = new Intervenant();
            $intervenant->setNom($faker->name);
            $intervenant->setPrenom($faker->firstName);
            $intervenant->setAnnee(new \DateTime());

            $manager->persist($intervenant);
        }
    }

    private function setSingleIntervenant(ObjectManager $manager, $faker)
    {
        $intervenant = new Intervenant();
        $intervenant->setNom($faker->name);
        $intervenant->setPrenom($faker->firstName);
        $intervenant->setAnnee(new \DateTime());

        $manager->persist($intervenant);
        return $intervenant;
    }


    private function setMatieres(ObjectManager $manager, $faker)
    {
        for($i = 1; $i<=10; $i++) {
            $matiere = new Matiere();

            $matiere->setNom($faker->name);
            $matiere->setDebutDate(new \DateTime(2020-05-01));
            $matiere->setFinDate(new \DateTime(2020-05-04));
            $matiere->setIntervenant($this->setSingleIntervenant($manager, $faker));
            $matiere->setClasse($this->setSingleClasse($manager, $faker));

            $manager->persist($matiere);
        }
    }
}
