<?php

namespace App\JsonApi\Hydrator\Disease;

use App\Entity\Disease;

/**
 * Update Disease Hydrator.
 */
class UpdateDiseaseHydrator extends AbstractDiseaseHydrator
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
