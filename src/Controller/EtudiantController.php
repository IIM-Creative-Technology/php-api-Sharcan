<?php

namespace App\Controller;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->etudiantRepository = $entityManager->getRepository(Etudiant::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="get_etudiants", methods={"GET"})
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getEtudiants(SerializerInterface $serializer): JsonResponse
    {
        $etudiants = $this->etudiantRepository->findAll();
        $context = SerializationContext::create()->setGroups(['etudiant']);
        $etudiants = $serializer->serialize($etudiants, 'json', $context);

        return JsonResponse::fromJsonString($etudiants, 200);
    }

    /**
     * @Route("/{id}", name="get_etudiant", methods={"GET"})
     * @param int $id
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getEtudiant(int $id, SerializerInterface $serializer): JsonResponse
    {
        $etudiant = $this->etudiantRepository->find($id);

        if(!$etudiant instanceof Etudiant) {
            throw new NotFoundHttpException('Etudiant introuvable');
        }
        $context = SerializationContext::create()->setGroups(['etudiant', 'etudiant_note', 'note']);
        $etudiant = $serializer->serialize($etudiant, 'json', $context);

        return JsonResponse::fromJsonString($etudiant, 200);
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
