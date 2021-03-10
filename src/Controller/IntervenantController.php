<?php

namespace App\Controller;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Entity\Intervenant;
use App\Repository\IntervenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/intervenant")
 */
class IntervenantController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var IntervenantRepository|ObjectRepository
     */
    private $intervenantRepository;

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
        $this->intervenantRepository = $entityManager->getRepository(Intervenant::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="get_intervenant", methods={"GET"})
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getIntervenants(SerializerInterface $serializer): JsonResponse
    {
        $intervenants = $this->intervenantRepository->findAll();
        $context = SerializationContext::create()->setGroups(['intervenant']);
        $intervenants = $serializer->serialize($intervenants, 'json', $context);

        return JsonResponse::fromJsonString($intervenants, 200);
    }

    /**
     * @Route("/{id}", name="get_intervenant_by_id", methods={"GET"})
     * @param int $id
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getIntervenant(int $id, SerializerInterface $serializer): JsonResponse
    {
        $intervenant = $this->intervenantRepository->find($id);

        if(!$intervenant instanceof Intervenant) {
            throw new NotFoundHttpException('Intervenant introuvable');
        }


        $context = SerializationContext::create()->setGroups(['intervenant', 'intervenant_matiere', 'matiere']);
        $intervenant = $serializer->serialize($intervenant, 'json', $context);

        return JsonResponse::fromJsonString($intervenant, 200);
    }

    /**
     * @Route("/", name="add_intervenant", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addIntervenant(Request $request): JsonResponse
    {
        $intervenant = new Intervenant();

        $intervenant->setNom($request->request->get('nom'));
        $intervenant->setPrenom($request->request->get('prenom'));
        $intervenant->setAnnee(new \DateTime($request->request->get('annee')));

        $this->entityManager->persist($intervenant);
        $this->entityManager->flush();

        return $this->json('Intervenant créé avec succès');
    }

    /**
     * @Route("/{id}", name="update_intervenant_by_id", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function updateIntervenant(Request $request, int $id): JsonResponse
    {
        $intervenant = $this->intervenantRepository->find($id);

        if(!$intervenant instanceof Intervenant) {
            throw new NotFoundHttpException('Intervenant introuvable');
        }

        $intervenant->setNom($request->request->get('nom'));
        $intervenant->setPrenom($request->request->get('prenom'));
        $intervenant->setAnnee(new \DateTime($request->request->get('annee')));

        $this->entityManager->persist($intervenant);
        $this->entityManager->flush();

        return $this->json('Intervenant modifié avec succès');
    }


    /**
     * @Route("/{id}", name="remove_intervenant_by_id", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteClasse(int $id){
        $intervenant = $this->intervenantRepository->find($id);

        if(!$intervenant instanceof Intervenant) {
            throw new NotFoundHttpException('Intervenant introuvable');
        }

        $this->entityManager->remove($intervenant);
        $this->entityManager->flush();

        return $this->json('Suppression terminé');
    }
}
