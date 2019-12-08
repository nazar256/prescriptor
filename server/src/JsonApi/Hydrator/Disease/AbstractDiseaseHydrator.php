<?php

namespace App\JsonApi\Hydrator\Disease;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Disease;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use Doctrine\ORM\Query\Expr;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract Disease Hydrator.
 */
abstract class AbstractDiseaseHydrator extends AbstractHydrator
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateClientGeneratedId(
        string $clientGeneratedId,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory
    ): void {
        if (!empty($clientGeneratedId)) {
            throw $exceptionFactory->createClientGeneratedIdNotSupportedException(
                $request,
                $clientGeneratedId
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function generateId(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['diseases'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($disease): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        $this->validateFields($this->objectManager->getClassMetadata(Disease::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($disease, string $id): void
    {
        if ($id && (string) $disease->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($disease): array
    {
        return [
            'drugs' => function (Disease $disease, ToManyRelationship $drugs, $data, $relationshipName) {
                $this->validateRelationType($drugs, ['drugs']);

                if (count($drugs->getResourceIdentifierIds()) > 0) {
                    $association = $this->objectManager->getRepository('App\Entity\Drug')
                        ->createQueryBuilder('d')
                        ->where((new Expr())->in('d.id', $drugs->getResourceIdentifierIds()))
                        ->getQuery()
                        ->getResult();

                    $this->validateRelationValues($association, $drugs->getResourceIdentifierIds(), $relationshipName);
                } else {
                    $association = [];
                }

                if ($disease->getDrugs()->count() > 0) {
                    foreach ($disease->getDrugs() as $drug) {
                        $disease->removeDrug($drug);
                    }
                }

                foreach ($association as $drug) {
                    $disease->addDrug($drug);
                }
            },
        ];
    }
}
