<?php

namespace WeGetFinancing\Checkout\Entity\Request;

use Magento\Framework\App\ProductMetadataInterface;
use WeGetFinancing\Checkout\Entity\AddressEntity;
use WeGetFinancing\Checkout\Entity\CartItem;
use WeGetFinancing\Checkout\Entity\EntityInterface;
use WeGetFinancing\Checkout\Exception\AddressEntityException;
use WeGetFinancing\Checkout\Exception\FunnelGeneratorRequestException;
use WeGetFinancing\Checkout\Validator\MandatoryFieldsArrayValidatorInterface;
use WeGetFinancing\Checkout\Service\Http\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Gateway\Config;
use Magento\Quote\Model\Quote;

class FunnelGeneratorRequest implements EntityInterface
{
    /**
     * @var string
     */
    protected string $firstName;

    /**
     * @var string
     */
    protected string $lastName;

    /**
     * @var string
     */
    protected string $telephone;

    /**
     * @var string
     */
    protected string $email;

    /**
     * @var string
     */
    protected string $shippingAmount;

    /**
     * @var int
     */
    protected int $merchantTransactionId;

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @var array|string[]
     */
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

    /**
     * @var array|string[]
     */
    private array $sessionMandatoryFields = ['shipping_amount'];

    /**
     * FunnelGeneratorRequest Constructor.
     *
     * @param MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator
     * @param AddressEntity $address
     * @param Config $config
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        protected MandatoryFieldsArrayValidatorInterface $mandatoryFieldsValidator,
        protected AddressEntity $address,
        protected Config $config,
        protected ProductMetadataInterface $productMetadata
    ) {
    }

    /**
     * Init From Array
     *
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
     * Init From Array And Session
     *
     * @param array $array
     * @param Quote $quote
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

    /**
     * To Array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'shippingAmount' => $this->shippingAmount,
            'version' => $this->config->getApiVersion(),
            'email' => $this->email,
            'phone' => $this->telephone,
            'merchantTransactionId' => (string) $this->merchantTransactionId,
            'success_url' => '',
            'failure_url' => '',
            'software_name' => $this->productMetadata->getName(),
            'software_version' => $this->productMetadata->getVersion(),
            'software_plugin_version' => '-',
            'postback_url' => $this->config->getPostBackUrl(),
            'billingAddress' => $this->address->toArray(),
            'shippingAddress' => $this->address->toArray(),
            'cartItems' => $this->items
        ];
    }

    /**
     * Common Init
     *
     * @param Quote $quote
     * @return $this
     * @throws EntityValidationException
     * @throws FunnelGeneratorRequestException
     */
    protected function commonInit(Quote $quote): self
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
