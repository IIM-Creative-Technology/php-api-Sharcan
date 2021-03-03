<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/classe")
 */
class ClasseController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Repository\ClasseRepository|\Doctrine\Persistence\ObjectRepository
     */
    private $classeRepository;

    /**
     * @var SerializerInterface
     */
    private $serialize;

    /**
     * ClasseController constructor.
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->classeRepository = $entityManager->getRepository(Classe::class);
        $this->entityManager = $entityManager;
        $this->serialize = $serializer;
    }

    /**
     * @Route("/", name="get_classes", methods={"GET"})
     */
    public function getClasses(): JsonResponse
    {
        $classes = $this->classeRepository->findAll();
        $json = $this->serialize->serialize($classes, 'json', ['groups' => ['classe']]);
        return JsonResponse::fromJsonString($json, 200);
    }

    /**
     * @Route("/{id}", name="get_classe_by_id", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function getClasse(int $id): JsonResponse
    {
        $classe = $this->classeRepository->find($id);
        $json = $this->serialize->serialize($classe, 'json', ['groups' => ['classe']]);
        return JsonResponse::fromJsonString($json, 200);
    }


    /**
     * @Route("/{id}/etudiant", name="get_classe_by_id_with_student", methods={"GET"})
     */
    public function getClasseWithEtudiant(int $id): Response
    {
        $classe = $this->classeRepository->find($id);
        $json = $this->serialize->serialize($classe, 'json', ['groups' => ['classe', 'classe_etudiants', 'etudiant']]);
        return JsonResponse::fromJsonString($json, 200);
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
    public function deleteClasse(int $id){
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
    public function updateClasse(int $id, Request $request) {
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
