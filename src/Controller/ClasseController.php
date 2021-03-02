<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/classe")
 */
class ClasseController extends AbstractController
{

    private $entityManager;

    private $classeRepository;

    /**
     * ClasseController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->classeRepository = $entityManager->getRepository(Classe::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="get_classe", methods={"GET"})
     */
    public function getClasses(): Response
    {
        $classes = $this->classeRepository->findAll();
        return $this->json($classes);
    }

    /**
     * @Route("/{id}", name="get_classe_by_id", methods={"GET"})
     */
    public function getClasse($id): Response
    {
        $classe = $this->classeRepository->find($id);
        return $this->json($classe);
    }

    /**
     * @Route("/", name="add_classe", methods={"POST"})
     * @param Request $request
     */
    public function addClasse(Request $request)
    {
        $classe = new Classe();

        $classe->setName($request->request->get('name'));
        $classe->setAnnee(new \DateTime($request->request->get('annee')));

        $this->entityManager->persist($classe);
        $this->entityManager->flush();

        return $this->json('Ajout de la classe terminé');
    }

    /**
     * @Route("/{id}", name="remove_classe_by_id", methods={"DELETE"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteClasse($id){
        $classe = $this->classeRepository->find($id);

        if(!$classe instanceof Classe) {
            throw new NotFoundHttpException('Classe introuvable');
        }

        $this->entityManager->remove($classe);
        $this->entityManager->flush();

        return $this->json('Suppression terminé');
    }

    /**
     * @Route("/{id}", name="update_classe_by_id", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function updateClasse($id, Request $request) {
        $classe = $this->classeRepository->find($id);

        if(!$classe instanceof Classe) {
            throw new NotFoundHttpException('Classe introuvable');
        }

        $classe->setName($request->request->get('name'));
        $classe->setAnnee(new \DateTime($request->request->get('annee')));

        $this->entityManager->persist($classe);
        $this->entityManager->flush();

        return $this->json('Mise à jour de la classe terminé');
    }
}
