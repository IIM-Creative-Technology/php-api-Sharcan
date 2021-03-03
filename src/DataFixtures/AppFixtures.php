<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use App\Entity\Etudiant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for($i=1; $i<6; $i++) {
            $classe = new Classe();
            $classe->setName('Promotion n° ' . $i);
            $classe->setAnnee(new \DateTime());

            $manager->persist($classe);

            $this->setEtudiant($classe, $manager);
        }

        $manager->flush();

    }


    private function setEtudiant(Classe $classe, ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');

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
}
