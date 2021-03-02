<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for($i=1; $i<6; $i++) {
            $classe = new Classe();
            $classe->setName('Promotion n° ' . $i);
            $classe->setAnnee(new \DateTime());

            $manager->persist($classe);
        }

        $manager->flush();
    }
}
