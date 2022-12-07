<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface SetOrderInvIdInterface
{
    /**
     * @param string $request
     * @return string
     */
    public function setOrderInvId(string $request): string;

}
