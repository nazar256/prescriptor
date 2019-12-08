<?php

namespace App\JsonApi\Hydrator\Disease;

use App\Entity\Disease;

/**
 * Create Disease Hydrator.
 */
class CreateDiseaseHydrator extends AbstractDiseaseHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($disease): array
    {
        return [
            'name' => function (Disease $disease, $attribute, $data, $attributeName) {
                $disease->setName($attribute);
            },
        ];
    }
}
