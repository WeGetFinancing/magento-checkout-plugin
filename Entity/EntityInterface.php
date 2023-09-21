<?php

namespace WeGetFinancing\Checkout\Entity;

interface EntityInterface
{
    /**
     * Init From Array
     *
     * @param array $array
     * @return EntityInterface
     */
    public function initFromArray(array $array): EntityInterface;

    /**
     * To Array
     *
     * @return array
     */
    public function toArray(): array;
}
