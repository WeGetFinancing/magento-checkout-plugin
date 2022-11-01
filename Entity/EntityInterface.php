<?php

namespace WeGetFinancing\Checkout\Entity;

interface EntityInterface
{
    /**
     * @param array $array
     * @return EntityInterface
     */
    public function initFromArray(array $array): EntityInterface;

    /**
     * @return array
     */
    public function toArray(): array;
}
