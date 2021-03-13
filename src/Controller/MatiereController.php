<?php

namespace App\Controller;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Entity\Classe;
use App\Entity\Intervenant;
use App\Entity\Matiere;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;


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
     * ClasseController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->matiereRepository = $entityManager->getRepository(Matiere::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="get_matiere", methods={"GET"})
     * @OA\Tag(name="Matiere")
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getMatieres(SerializerInterface $serializer): JsonResponse
    {
        $matieres = $this->matiereRepository->findAll();
        $context = SerializationContext::create()->setGroups(['matiere']);
        $matieres = $serializer->serialize($matieres, 'json', $context);

        return JsonResponse::fromJsonString($matieres, 200);
    }

    /**
     * @Route("/{id}", name="get_matiere_by_id", methods={"GET"})
     * @OA\Tag(name="Matiere")
     * @param int $id
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getMatiere(int $id, SerializerInterface $serializer): JsonResponse
    {
        $matiere = $this->matiereRepository->find($id);
        if(!$matiere instanceof Matiere) {
            throw new NotFoundHttpException('Matière introuvable');
        }
        $context = SerializationContext::create()->setGroups(['matiere', 'matiere_classe', 'classe', 'matiere_intervenant', 'intervenant']);
        $matiere = $serializer->serialize($matiere, 'json', $context);

        return JsonResponse::fromJsonString($matiere, 200);
    }

    /**
     * @Route("/", name="add_matiere", methods={"POST"})
     * @OA\Tag(name="Matiere")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Matiere::class, groups={"matiere"}))
     * ))
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
        if($interval->d < 1 || $interval->d > 5) {
            throw new \Exception("L'intervale des dates doit être comprise en 1 et 5 jours");
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
     * @OA\Tag(name="Matiere")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Matiere::class, groups={"matiere"}))
     * ))
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

        $startDate = new \DateTime($request->request->get('debut'));
        $endDate = new \DateTime($request->request->get('fin'));

        $interval = $endDate->diff($startDate);
        if($interval->d < 1 || $interval->d > 5) {
            throw new \Exception("L'intervale des dates doit être comprise en 1 et 5 jours");
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
     * @OA\Tag(name="Matiere")
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
