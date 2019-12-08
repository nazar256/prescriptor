<?php

namespace App\JsonApi\Transformer;

use App\Entity\Disease;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Disease Resource Transformer.
 */
class DiseaseResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($disease): string
    {
        return 'diseases';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($disease): string
    {
        return (string) $disease->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($disease): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($disease): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/diseases/'.$this->getId($disease)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($disease): array
    {
        return [
            'name' => function (Disease $disease) {
                return $disease->getName();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($disease): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($disease): array
    {
        return [
            'drugs' => function (Disease $disease) {
                return ToManyRelationship::create()
                    ->setDataAsCallable(
                        function () use ($disease) {
                            return $disease->getDrugs();
                        },
                        new DrugResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
