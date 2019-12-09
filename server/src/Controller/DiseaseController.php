<?php

namespace App\Controller;

use App\Entity\Disease;
use App\JsonApi\Document\Disease\DiseaseDocument;
use App\JsonApi\Document\Disease\DiseasesDocument;
use App\JsonApi\Hydrator\Disease\CreateDiseaseHydrator;
use App\JsonApi\Hydrator\Disease\UpdateDiseaseHydrator;
use App\JsonApi\Transformer\DiseaseResourceTransformer;
use App\Queue\JournalQueue;
use App\Repository\DiseaseRepository;
use FOD\DBALClickHouse\Connection;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;

/**
 * @Route("/diseases")
 */
class DiseaseController extends Controller
{
    /**
     * @Route("/", name="diseases_index", methods="GET")
     */
    public function index(DiseaseRepository $diseaseRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($diseaseRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new DiseasesDocument(new DiseaseResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="diseases_new", methods="POST")
     */
    public function new(ValidatorInterface $validator, DefaultExceptionFactory $exceptionFactory): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $disease = $this->jsonApi()->hydrate(new CreateDiseaseHydrator($entityManager, $exceptionFactory), new Disease());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($disease);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($disease);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new DiseaseDocument(new DiseaseResourceTransformer()),
            $disease
        );
    }

    /**
     * @Route("/{id}", name="diseases_show", methods="GET")
     */
    public function show(Disease $disease, Request $request, JournalQueue $journalQueue): ResponseInterface
    {
        $patient = $request->get('patient', '');
        $journalQueue->addJournal($patient, new \DateTime(), $disease->getId());

        return $this->jsonApi()->respond()->ok(
            new DiseaseDocument(new DiseaseResourceTransformer()),
            $disease
        );
    }

    /**
     * @Route("/{id}", name="diseases_edit", methods="PATCH")
     */
    public function edit(Disease $disease, ValidatorInterface $validator, DefaultExceptionFactory $exceptionFactory): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $disease = $this->jsonApi()->hydrate(new UpdateDiseaseHydrator($entityManager, $exceptionFactory), $disease);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($disease);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new DiseaseDocument(new DiseaseResourceTransformer()),
            $disease
        );
    }

    /**
     * @Route("/{id}", name="diseases_delete", methods="DELETE")
     */
    public function delete(Disease $disease): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($disease);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}
