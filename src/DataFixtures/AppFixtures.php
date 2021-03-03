<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Intervenant;
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

            $this->setEtudiant($classe, $manager, $faker);

        }

        $this->setIntervenant($manager, $faker);

        $manager->flush();

    }


    private function setEtudiant(Classe $classe, ObjectManager $manager, $faker)
    {
        for($i=0; $i<=30; $i++) {
            $etudiant = new Etudiant();

            $etudiant->setNom($faker->name);
            $etudiant->setPrenom($faker->firstName);
            $etudiant->setAnnee(new \DateTime(random_int(2020, 2025)));
            $etudiant->setAge(random_int(15, 99));
            $etudiant->setPromotion($classe);

            $manager->persist($etudiant);

        }
    }

    private function setIntervenant(ObjectManager $manager, $faker)
    {

        $faker = Faker\Factory::create('fr_FR');

        for($i=1; $i<=20; $i++) {
            $intervenant = new Intervenant();
            $intervenant->setNom($faker->name);
            $intervenant->setPrenom($faker->firstName);
            $intervenant->setAnnee(new \DateTime());

            $manager->persist($intervenant);
        }
    }
}
