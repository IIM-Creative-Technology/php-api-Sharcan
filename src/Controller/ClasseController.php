<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

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
     * @var ClasseRepository|ObjectRepository
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
     * @OA\Tag(name="Classe")
     * @OA\Response(
     *     response="200",
     *     description="Classe response",
     *     @OA\JsonContent(ref=@Model(type=Classe::class))
     * )
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
     * @OA\Tag(name="Classe")
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
     * @OA\Tag(name="Classe")
     * @param int $id
     * @param SerializerInterface $serializer
     * @return Response
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
     * @OA\Tag(name="Classe")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Classe::class, groups={"classe"}))
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addClasse(Request $request): JsonResponse
    {
        var_dump($request->request->all());
        die;
        $classe = new Classe();

        $classe->setName($request->request->get('name'));
        $classe->setAnnee(new \DateTime($request->request->get('annee')));

        $this->entityManager->persist($classe);
        $this->entityManager->flush();

        return $this->json('Ajout de la classe terminé');
    }

    /**
     * @Route("/{id}", name="remove_classe_by_id", methods={"DELETE"})
     * @OA\Tag(name="Classe")
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
     * @OA\Tag(name="Classe")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Classe::class, groups={"classe"}))
     * )
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
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
