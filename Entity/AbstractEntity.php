<?php

namespace WeGetFinancing\Checkout\Entity;

abstract class AbstractEntity implements EntityInterface
{
    /**
     * AbstractEntity Constructor.
     *
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        if (false === empty($array)) {
            $this->initFromArray($array);
        }
    }
}
