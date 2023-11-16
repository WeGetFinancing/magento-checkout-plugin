<?php

declare(strict_types=1);

namespace WeGetFinancing\Checkout\Service\Http\SDK\Entity\Request;

use WeGetFinancing\Checkout\Service\Http\SDK\Entity\AbstractEntity;

abstract class AbstractRequestEntity extends AbstractEntity
{
    /**
     * @return array<string, mixed>
     */
    public function getWeGetFinancingRequest(): array
    {
        $reflection = new class () {
            /** @return array<string, mixed> */
            public function getPublicVars(AbstractRequestEntity $object): array
            {
                return get_object_vars($object);
            }
        };
        $publicVars = $reflection->getPublicVars($this);

        $request = [];

        foreach ($publicVars as $nameRaw => $value) {
            $name = $this->camelCaseToSnakeCase->normalize($nameRaw);
            $request[$name] = $value;
        }

        return $request;
    }
}
