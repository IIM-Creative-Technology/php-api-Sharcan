<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use OpenApi\Annotations as OA;


/**
 * @Route("/api/note")
 */
class NoteController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NoteRepository|ObjectRepository
     */
    private $noteRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * NoteController constructor.
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->noteRepository = $this->entityManager->getRepository(Note::class);
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="get_note", methods={"GET"})
     * @OA\Tag(name="Note")
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getNotes(): JsonResponse
    {
        $notes = $this->noteRepository->findAll();
        $context = SerializationContext::create()->setGroups(['note']);
        $notes = $this->serializer->serialize($notes, 'json', $context);

        return JsonResponse::fromJsonString($notes, 200);

    }

    /**
     * @Route("/{id}", name="get_note_by_id", methods={"GET"})
     * @OA\Tag(name="Note")
     * @param int $id
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getNote(int $id): JsonResponse
    {
        $note = $this->noteRepository->find($id);
        $context = SerializationContext::create()->setGroups(['note', 'note_etudiant', 'etudiant', 'note_matiere', 'matiere']);
        $note = $this->serializer->serialize($note, 'json', $context);

        return JsonResponse::fromJsonString($note, 200);

    }

    /**
     * @Route("/", name="add_note", methods={"POST"})
     * @OA\Tag(name="Note")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Note::class, groups={"note"}))
     * ))
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addNote(Request $request): JsonResponse
    {
        $matiere = $this->entityManager->getRepository(Matiere::class)->find($request->request->get('matiere'));
        $etudiant = $this->entityManager->getRepository(Etudiant::class)->find($request->request->get('etudiant'));

        if(!$matiere instanceof Matiere) {
            throw new NotFoundHttpException('Matiere introuvable');
        }
        if(!$etudiant instanceof Etudiant) {
            throw new NotFoundHttpException('Etudiant introuvable');
        }
        if($request->request->get('note') < 0 || $request->request->get('note') > 20) {
            throw new \InvalidArgumentException('La note doit être comprise entre 0 et 20.');
        }

        $note = new Note();
        $note->setNote($request->request->get('note'));
        $note->setEtudiant($etudiant);
        $note->setMatiere($matiere);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return $this->json('Note créée avec succès');
    }

    /**
     * @Route("/{id}", name="update_note_by_id", methods={"PUT"})
     * @OA\Tag(name="Note")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=Note::class, groups={"note"}))
     * ))
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNote(int $id, Request $request): JsonResponse
    {
        $note = $this->noteRepository->find($id);
        $matiere = $this->entityManager->getRepository(Matiere::class)->find($request->request->get('matiere'));
        $etudiant = $this->entityManager->getRepository(Etudiant::class)->find($request->request->get('etudiant'));

        if(!$note instanceof Note) {
            throw new NotFoundHttpException('Note introuvable');
        }
        if(!$matiere instanceof Matiere) {
            throw new NotFoundHttpException('Matiere introuvable');
        }
        if(!$etudiant instanceof Etudiant) {
            throw new NotFoundHttpException('Etudiant introuvable');
        }
        if($request->request->get('note') < 0 || $request->request->get('note') > 20) {
            throw new \InvalidArgumentException('La note doit être comprise entre 0 et 20.');
        }

        $note->setNote($request->request->get('note'));
        $note->setEtudiant($etudiant);
        $note->setMatiere($matiere);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return $this->json('Note modifiée avec succès');
    }

    /**
     * @Route("/{id}", name="remove_note_by_id", methods={"DELETE"})
     * @OA\Tag(name="Note")
     * @param int $id
     * @return JsonResponse
     */
    public function removeNote(int $id): JsonResponse
    {
        $note = $this->noteRepository->find($id);
        if(!$note instanceof Note) {
            throw new NotFoundHttpException('Note introuvable');
        }

        $this->entityManager->remove($note);
        $this->entityManager->flush();

        return $this->json('Note supprimée avec succès');
    }
}
