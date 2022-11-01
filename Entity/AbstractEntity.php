<?php

namespace WeGetFinancing\Checkout\Entity;

abstract class AbstractEntity implements EntityInterface
{
    public function __construct(array $array = [])
    {
        if (false === empty($array)) {
            $this->initFromArray($array);
        }
    }
}
