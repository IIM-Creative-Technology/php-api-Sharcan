<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Intervenant;
use App\Entity\Matiere;
use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
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
        $this->setNotes($manager, $faker);
        $this->setAdmin($manager);

        $manager->flush();

    }


    /**
     * @param ObjectManager $manager
     * @param $faker
     * @return Classe
     * @throws \Exception
     */
    private function setSingleClasse(ObjectManager $manager, $faker)
    {
        $classe = new Classe();
        $classe->setName('Promotion n° ' . random_int(30, 999));
        $classe->setAnnee(new \DateTime());

        $manager->persist($classe);

        return $classe;
    }

    /**
     * @param Classe $classe
     * @param ObjectManager $manager
     * @param $faker
     * @throws \Exception
     */
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

    /**
     * @param Classe $classe
     * @param ObjectManager $manager
     * @param $faker
     * @return Etudiant
     * @throws \Exception
     */
    private function setSingleEtudiant(Classe $classe, ObjectManager $manager, $faker)
    {
        $etudiant = new Etudiant();

        $etudiant->setNom($faker->name);
        $etudiant->setPrenom($faker->firstName);
        $etudiant->setAnnee(new \DateTime(random_int(2020, 2025)));
        $etudiant->setAge(random_int(15, 99));
        $etudiant->setPromotion($classe);

        return $etudiant;

    }

    /**
     * @param ObjectManager $manager
     * @param $faker
     */
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

    /**
     * @param ObjectManager $manager
     * @param $faker
     * @return Intervenant
     */
    private function setSingleIntervenant(ObjectManager $manager, $faker)
    {
        $intervenant = new Intervenant();
        $intervenant->setNom($faker->name);
        $intervenant->setPrenom($faker->firstName);
        $intervenant->setAnnee(new \DateTime());

        $manager->persist($intervenant);
        return $intervenant;
    }


    /**
     * @param ObjectManager $manager
     * @param $faker
     * @throws \Exception
     */
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

    /**
     * @param ObjectManager $manager
     * @param $faker
     * @return Matiere
     * @throws \Exception
     */
    private function setSingleMatiere(ObjectManager $manager, $faker)
    {
        $matiere = new Matiere();

        $matiere->setNom($faker->name);
        $matiere->setDebutDate(new \DateTime(2020-05-01));
        $matiere->setFinDate(new \DateTime(2020-05-04));
        $matiere->setIntervenant($this->setSingleIntervenant($manager, $faker));
        $matiere->setClasse($this->setSingleClasse($manager, $faker));

        return $matiere;
    }


    /**
     * @param ObjectManager $manager
     * @param $faker
     * @throws \Exception
     */
    private function setNotes(ObjectManager $manager, $faker)
    {
        for($i=1; $i<20; $i++) {
            $note = new Note();
            $classe = $this->setSingleClasse($manager, $faker);
            $etudiant = $this->setSingleEtudiant($classe, $manager, $faker);

            $note->setNote(random_int(0, 20));
            $note->setMatiere($this->setSingleMatiere($manager, $faker));
            $note->setEtudiant($etudiant);

            $manager->persist($note);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    private function setAdmin(ObjectManager $manager) {
        $listAdmin = ['Karine', 'Nicolas', 'Alexis'];

        foreach ($listAdmin as $admin) {
            $newAdmin = new Admin();
            $newAdmin->setEmail($admin . '@devinci.fr');
            $newAdmin->setPassword($this->passwordEncoder->encodePassword($newAdmin, 'password'));

            $manager->persist($newAdmin);
        }

    }

}
