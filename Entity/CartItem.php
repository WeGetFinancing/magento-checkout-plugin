<?php

namespace WeGetFinancing\Checkout\Entity;

use WeGetFinancing\Checkout\Validator\MandatoryFieldsArrayValidator;
use WeGetFinancing\Checkout\Validator\MandatoryFieldsArrayValidatorInterface;
use WeGetFinancing\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Exception\CartItemException;

class CartItem implements EntityInterface
{
    protected string $sku;

    protected string $displayName;

    protected string $unitPrice;

    protected string $quantity;

    protected string $unitTax;

    private array $mandatoryFields = [
        'sku',
        'displayName',
        'unitPrice',
        'quantity',
        'unitTax'
    ];

    public function __construct(
        protected MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator
    ) { }

    static public function make(): self
    {
        return new CartItem(new MandatoryFieldsArrayValidator());
    }

    /**
     * @param array $array
     * @return EntityInterface
     * @throws EntityValidationException
     */
    public function initFromArray(array $array): EntityInterface
    {
        if (false === $this->mandatoryFieldsValidator->validate($this->mandatoryFields, $array)) {
            throw new EntityValidationException(
                CartItemException::CART_INIT_WITH_INVALID_DATA_ERROR_MESSAGE,
                CartItemException::CART_INIT_WITH_INVALID_DATA_ERROR_CODE,
                null,
                $this->mandatoryFieldsValidator->getValidationErrors()
            );
        }

        $this->setSku($array['sku']);
        $this->setDisplayName($array['displayName']);
        $this->setUnitPrice($array['unitPrice']);
        $this->setQuantity($array['quantity']);
        $this->setUnitTax($array['unitTax']);

        return $this;
    }

    public function setSku(string $sku): EntityInterface
    {
        $this->sku = $sku;
        return $this;
    }

    public function setDisplayName(string $displayName): EntityInterface
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function setUnitPrice(string $unitPrice): EntityInterface
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function setQuantity(string $quantity): EntityInterface
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setUnitTax(string $unitTax): EntityInterface
    {
        $this->unitTax = $unitTax;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'displayName' => $this->displayName,
            'unitPrice' => $this->unitPrice,
            'quantity' => (int)$this->quantity,
            'unitTax' => $this->unitTax
        ];
    }
}
