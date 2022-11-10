<?php

namespace WeGetFinancing\Checkout\Entity\Request;

use WeGetFinancing\Checkout\Entity\AddressEntity;
use WeGetFinancing\Checkout\Entity\CartItem;
use WeGetFinancing\Checkout\Entity\EntityInterface;
use WeGetFinancing\Checkout\Exception\AddressEntityException;
use WeGetFinancing\Checkout\Exception\FunnelGeneratorRequestException;
use WeGetFinancing\Checkout\Validator\MandatoryFieldsArrayValidatorInterface;
use WeGetFinancing\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Gateway\Config;
use Magento\Quote\Model\Quote;

class FunnelGeneratorRequest implements EntityInterface
{
    protected MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator;

    protected string $firstName;

    protected string $lastName;

    protected string $telephone;

    protected string $email;

    protected string $shippingAmount;

    protected AddressEntity $address;

    protected int $merchantTransactionId;

    protected array $items = [];

    private Config $config;

    private array $mandatoryFields = [
        'firstname',
        'lastname',
        'street[0]',
        'region_id',
        'city',
        'postcode',
        'telephone',
        'email',
        'shipping_amount',
        'quote'
    ];

    private array $sessionMandatoryFields = ['shipping_amount'];

    public function __construct(
        MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator,
        AddressEntity $address,
        Config $config
    ) {
        $this->mandatoryFieldsValidator = $mandatoryFieldsValidator;
        $this->address = $address;
        $this->config = $config;
    }

    /**
     * @param array $array
     * @return EntityInterface
     * @throws AddressEntityException
     * @throws EntityValidationException
     * @throws FunnelGeneratorRequestException
     */
    public function initFromArray(array $array): EntityInterface
    {
        if (false === $this->mandatoryFieldsValidator->validate($this->mandatoryFields, $array)) {
            throw new EntityValidationException(
                FunnelGeneratorRequestException::INVALID_REQUEST_ERROR_MESSAGE,
                FunnelGeneratorRequestException::INVALID_REQUEST_ERROR_CODE,
                null,
                $this->mandatoryFieldsValidator->getValidationErrors()
            );
        }

        $this->firstName = $array['firstname'];
        $this->lastName = $array['lastname'];
        $this->telephone = $array['telephone'];
        $this->email = $array['email'];
        $this->shippingAmount = $array['shipping_amount'];

        $street = (true === isset($array['street[1]']) && false === empty($array['street[1]']))
            ? $array['street[0]'] . ' ' . $array['street[1]']
            : $array['street[0]'];

        $this->address->initFromArray([
            'street1' => $street,
            'state' => $array['region_id'],
            'city' => $array['city'],
            'zipcode' => $array['postcode']
        ]);

        /** @var Quote $quote */
        $quote = $array['quote'];

        return $this->commonInit($quote);
    }

    /**
     * @param array $array
     * @return EntityInterface
     * @throws AddressEntityException
     * @throws EntityValidationException
     * @throws FunnelGeneratorRequestException
     */
    public function initFromArrayAndSession(array $array, Quote $quote): EntityInterface
    {
        if (false === $this->mandatoryFieldsValidator->validate($this->sessionMandatoryFields, $array)) {
            throw new EntityValidationException(
                FunnelGeneratorRequestException::INVALID_REQUEST_ERROR_MESSAGE,
                FunnelGeneratorRequestException::INVALID_REQUEST_ERROR_CODE,
                null,
                $this->mandatoryFieldsValidator->getValidationErrors()
            );
        }

        $customer = $quote->getCustomer();
        $address = $quote->getShippingAddress();

        $this->firstName = $address->getFirstname();
        $this->lastName = $address->getLastname();
        $this->telephone = $address->getTelephone();
        $this->email = $customer->getEmail();
        $this->shippingAmount = $array['shipping_amount'];

        $this->address->initFromArray([
            'street1' => $address->getStreetFull(),
            'state' => $address->getRegionId(),
            'city' => $address->getCity(),
            'zipcode' => $address->getPostcode()
        ]);

        return $this->commonInit($quote);
    }

    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'shippingAmount' => $this->shippingAmount,
            'version' => $this->config->getWeGetFinancingVersion(),
            'email' => $this->email,
            'phone' => $this->telephone,
            'merchantTransactionId' => (string) $this->merchantTransactionId,
            'success_url' => '',
            'failure_url' => '',
            'postback_url' => $this->config->getWeGetFinancingPostBackUrl(),
            'billingAddress' => $this->address->toArray(),
            'shippingAddress' => $this->address->toArray(),
            'cartItems' => $this->items
        ];
    }

    /**
     * @param Quote $quote
     * @return $this
     * @throws EntityValidationException
     * @throws FunnelGeneratorRequestException
     */
    protected function commonInit(Quote $quote)
    {
        $quoteId = $quote->getId();
        if (true === empty($quoteId)) {
            throw new FunnelGeneratorRequestException(
                FunnelGeneratorRequestException::QUOTE_NOT_FOUND_ERROR_MESSAGE,
                FunnelGeneratorRequestException::QUOTE_NOT_FOUND_ERROR_CODE
            );
        }
        $this->merchantTransactionId = $quoteId;

        foreach ($quote->getAllVisibleItems() as $item) {
            $this->items[] = CartItem::make()->initFromArray([
                'sku' => $item->getSku(),
                'displayName' => $item->getName(),
                'unitPrice' => $item->getPrice() - (($item->getPrice() / 100) * $item->getDiscountPercent()),
                'quantity' => $item->getQty(),
                'unitTax' => $item->getTaxAmount()
            ])->toArray();
        }

        if (true === empty($this->items)) {
            throw new FunnelGeneratorRequestException(
                FunnelGeneratorRequestException::QUOTE_WITH_NO_ITEMS_ERROR_MESSAGE,
                FunnelGeneratorRequestException::QUOTE_WITH_NO_ITEMS_ERROR_CODE
            );
        }

        return $this;
    }
}
