<?php

namespace App\JsonApi\Transformer;

use App\Entity\Drug;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

class DrugResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($drug): string
    {
        return 'drugs';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($drug): string
    {
        return (string)$drug->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($drug): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($drug): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/drugs/' . $this->getId($drug)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($drug): array
    {
        return [
            'name' => function (Drug $drug) {
                return $drug->getName();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($drug): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($drug): array
    {
        return [
            'diseases' => function (Drug $drug) {
                return ToManyRelationship::create()
                    ->setDataAsCallable(
                        function () use ($drug) {
                            return $drug->getDiseases();
                        },
                        new DrugResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
