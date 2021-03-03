<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/etudiant")
 */
class EtudiantController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EtudiantRepository|ObjectRepository
     */
    private $etudiantRepository;

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
        $this->etudiantRepository = $entityManager->getRepository(Etudiant::class);
        $this->entityManager = $entityManager;
        $this->serialize = $serializer;
    }

    /**
     * @Route("/", name="get_etudiants", methods={"GET"})
     * @return JsonResponse
     */
    public function getEtudiants(): JsonResponse
    {
        $etudiants = $this->etudiantRepository->findAll();

        $json = $this->serialize->serialize($etudiants, 'json', ['groups' => ['etudiant', 'classe_information']]);

        return JsonResponse::fromJsonString($json, 200);
    }

    /**
     * @Route("/{id}", name="get_etudiant", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function getEtudiant(int $id): JsonResponse
    {
        $etudiant = $this->etudiantRepository->find($id);
        $json = $this->serialize->serialize($etudiant, 'json', ['groups' => ['etudiant']]);

        return JsonResponse::fromJsonString($json, 200);
    }

    /**
     * @Route("/", name="add_etudiant", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function addEtudiant(Request $request): JsonResponse
    {
        $etudiant = new Etudiant();

        $classe = $this->entityManager->getRepository(Classe::class)->find($request->request->get('classe_id'));

        if(!$classe instanceof Classe) {
            throw new NotFoundHttpException('La classe renseigné n\'existe pas');
        }

        $etudiant->setNom($request->request->get('nom'));
        $etudiant->setPrenom($request->request->get('prenom'));
        $etudiant->setAge($request->request->get('age'));
        $etudiant->setAnnee(new \DateTime($request->request->get('annee')));
        $etudiant->setPromotion($classe);

        $this->entityManager->persist($etudiant);
        $this->entityManager->flush();

        return $this->json('Etudiant créé avec succès');
    }

    /**
     * @Route("/{id}", name="remove_etudiant_by_id", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteEtudiant(int $id): JsonResponse
    {
        $etudiant = $this->etudiantRepository->find($id);

        if(!$etudiant instanceof Etudiant) {
            throw new NotFoundHttpException('Etudiant introuvable', null, 404);
        }

        $this->entityManager->remove($etudiant);
        $this->entityManager->flush();

        return $this->json('Suppression terminé');
    }

    /**
     * @Route("/{id}", name="update_etudiant_by_id", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateEtudiant(int $id, Request $request): JsonResponse
    {
        $etudiant = $this->etudiantRepository->find($id);

        if(!$etudiant instanceof Etudiant) {
            throw new NotFoundHttpException('Etudiant introuvable', null, 404);
        }

        $classe = $this->entityManager->getRepository(Classe::class)->find($request->request->get('classe_id'));

        if(!$classe instanceof Classe) {
            throw new NotFoundHttpException('La classe renseigné n\'existe pas');
        }

        $etudiant->setNom($request->request->get('nom'));
        $etudiant->setPrenom($request->request->get('prenom'));
        $etudiant->setAge($request->request->get('age'));
        $etudiant->setAnnee(new \DateTime($request->request->get('annee')));
        $etudiant->setPromotion($classe);

        $this->entityManager->persist($etudiant);
        $this->entityManager->flush();

        return $this->json('Update terminé');
    }
}
