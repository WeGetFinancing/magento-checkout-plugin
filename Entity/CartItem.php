<?php

namespace WeGetFinancing\Checkout\Entity;

use WeGetFinancing\Checkout\Validator\MandatoryFieldsArrayValidator;
use WeGetFinancing\Checkout\Validator\MandatoryFieldsArrayValidatorInterface;
use WeGetFinancing\Checkout\Service\Http\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Exception\CartItemException;

class CartItem implements EntityInterface
{
    /**
     * @var string
     */
    protected string $sku;

    /**
     * @var string
     */
    protected string $displayName;

    /**
     * @var string
     */
    protected string $unitPrice;

    /**
     * @var string
     */
    protected string $quantity;

    /**
     * @var string
     */
    protected string $unitTax;

    /**
     * @var array|string[]
     */
    private array $mandatoryFields = [
        'sku',
        'displayName',
        'unitPrice',
        'quantity',
        'unitTax'
    ];

    /**
     * CartItem Constructor.
     *
     * @param MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator
     */
    public function __construct(
        protected MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator
    ) {
    }

    /**
     * Make
     *
     * @return self
     */
    public static function make(): self
    {
        return new CartItem(new MandatoryFieldsArrayValidator());
    }

    /**
     * Init From Array
     *
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

    /**
     * Set SKU
     *
     * @param string $sku
     * @return EntityInterface
     */
    public function setSku(string $sku): EntityInterface
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * Set Display Name
     *
     * @param string $displayName
     * @return EntityInterface
     */
    public function setDisplayName(string $displayName): EntityInterface
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Set Unit Price
     *
     * @param string $unitPrice
     * @return EntityInterface
     */
    public function setUnitPrice(string $unitPrice): EntityInterface
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * Set Quantity
     *
     * @param string $quantity
     * @return EntityInterface
     */
    public function setQuantity(string $quantity): EntityInterface
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Set Unit Tax
     *
     * @param string $unitTax
     * @return EntityInterface
     */
    public function setUnitTax(string $unitTax): EntityInterface
    {
        $this->unitTax = $unitTax;
        return $this;
    }

    /**
     * To Array
     *
     * @return array
     */
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
