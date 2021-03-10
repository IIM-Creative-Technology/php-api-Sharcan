<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/classe")
 */
class ClasseController extends BaseController
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
     * ClasseController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->classeRepository = $entityManager->getRepository(Classe::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="get_classes", methods={"GET"})
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getClasses(serializerInterface $serializer): JsonResponse
    {

        $classes = $this->classeRepository->findAll();
        $context = SerializationContext::create()->setGroups(['classe']);
        $classes = $serializer->serialize($classes, 'json', $context);
        return JsonResponse::fromJsonString($classes, 200);
    }

    /**
     * @Route("/{id}", name="get_classe_by_id", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function getClasse(int $id, serializerInterface $serializer): JsonResponse
    {
        $classe = $this->classeRepository->find($id);
        if(!$classe instanceof Classe){
            throw new NotFoundHttpException('Classe introuvable');
        }

        $context = SerializationContext::create()->setGroups(['classe']);
        $classe = $serializer->serialize($classe, 'json', $context);
        return JsonResponse::fromJsonString($classe, 200);
    }


    /**
     * @Route("/{id}/etudiant", name="get_classe_by_id_with_student", methods={"GET"})
     */
    public function getClasseWithEtudiant(int $id, serializerInterface $serializer): Response
    {
        $classe = $this->classeRepository->find($id);
        if(!$classe instanceof Classe){
            throw new NotFoundHttpException('Classe introuvable');
        }

        $context = SerializationContext::create()->setGroups(['classe', 'classe_etudiant', 'etudiant']);
        $classe = $serializer->serialize($classe, 'json', $context);
        return JsonResponse::fromJsonString($classe, 200);
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
     * @return JsonResponse
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
     * @return JsonResponse
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
