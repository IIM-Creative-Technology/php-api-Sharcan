<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Intervenant;
use App\Entity\Matiere;
use App\Repository\MatiereRepository;
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
 * @Route("/api/matiere")
 */
class MatiereController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MatiereRepository|ObjectRepository
     */
    private $matiereRepository;

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
        $this->matiereRepository = $entityManager->getRepository(Matiere::class);
        $this->entityManager = $entityManager;
        $this->serialize = $serializer;
    }

    /**
     * @Route("/", name="get_matiere", methods={"GET"})
     */
    public function getMatieres(): JsonResponse
    {
        $matieres = $this->matiereRepository->findAll();
         $json = $this->serialize->serialize($matieres, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);
         return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/{id}", name="get_matiere_by_id", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function getMatiere(int $id): JsonResponse
    {
        $matiere = $this->matiereRepository->find($id);
        if(!$matiere instanceof Matiere) {
            throw new NotFoundHttpException('Matière introuvable');
        }
        $json = $this->serialize->serialize($matiere, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return JsonResponse::fromJsonString($json, 200);
    }

    /**
     * @Route("/", name="add_matiere", methods={"POST"})
     * @param Request $request
     * @throws \Exception
     */
    public function addMatiere(Request $request)
    {

        $classe = $this->entityManager->getRepository(Classe::class)->find($request->request->get('classe'));
        $intervenant = $this->entityManager->getRepository(Intervenant::class)->find($request->request->get('intervenant'));

        if(!$classe instanceof Classe) {
            throw new NotFoundHttpException('Classe introuvable');
        }
        if(!$intervenant instanceof Intervenant) {
           throw new NotFoundHttpException('Intervenant introuvable');
        }

        $startDate = new \DateTime($request->request->get('debut'));
        $endDate = new \DateTime($request->request->get('fin'));

        $interval = $endDate->diff($startDate);
        if($interval->d > 5) {
            throw new \Exception("L'intervale des dates ne peut dépasser 5 jours");
        }


        $matiere = new Matiere();
        $matiere->setNom($request->request->get('nom'));
        $matiere->setDebutDate($startDate);
        $matiere->setFinDate($endDate);
        $matiere->setClasse($classe);
        $matiere->setIntervenant($intervenant);

        $this->entityManager->persist($matiere);
        $this->entityManager->flush();

        return $this->json('Matière créée avec succès');
    }

    /**
     * @Route("/{id}", name="update_matiere_by_id", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateMatiere(int $id, Request $request): JsonResponse
    {
        $matiere = $this->matiereRepository->find($id);
        if(!$matiere instanceof Matiere) {
            throw new NotFoundHttpException('Matière introuvable');
        }

        $classe = $this->entityManager->getRepository(Classe::class)->find($request->request->get('classe'));
        $intervenant = $this->entityManager->getRepository(Intervenant::class)->find($request->request->get('intervenant'));

        if(!$classe instanceof Classe) {
            throw new NotFoundHttpException('Classe introuvable');
        }
        if(!$intervenant instanceof Intervenant) {
            throw new NotFoundHttpException('Intervenant introuvable');
        }

        $matiere->setNom($request->request->get('nom'));
        $matiere->setDebutDate(new \DateTime($request->request->get('debut')));
        $matiere->setFinDate(new \DateTime($request->request->get('fin')));
        $matiere->setClasse($classe);
        $matiere->setIntervenant($intervenant);

        $this->entityManager->persist($matiere);
        $this->entityManager->flush();

        return $this->json('Matière modifiée avec succès');
    }

    /**
     * @Route("/{id}", name="delete_matiere_by_id", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function deleteMatiere(int $id)
    {
        $matiere = $this->matiereRepository->find($id);
        if(!$matiere instanceof Matiere) {
            throw new NotFoundHttpException('Matière introuvable');
        }

        $this->entityManager->remove($matiere);
        $this->entityManager->flush();

        return $this->json('Matière supprimée avec succès');
    }
}
