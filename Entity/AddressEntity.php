<?php

namespace WeGetFinancing\Checkout\Entity;

use Magento\Directory\Model\RegionFactory;
use WeGetFinancing\Checkout\Exception\AddressEntityException;

class AddressEntity implements EntityInterface
{
    /**
     * @var string
     */
    protected string $street1;

    /**
     * @var string
     */
    protected string $state;

    /**
     * @var string
     */
    protected string $city;

    /**
     * @var string
     */
    protected string $zipcode;

    /**
     * AddressEntity Constructor.
     *
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        private RegionFactory $regionFactory
    ) {
    }

    /**
     * Init From Array
     *
     * @param array $array
     * @return EntityInterface
     * @throws AddressEntityException
     */
    public function initFromArray(array $array): EntityInterface
    {
        $this->setCity($array['city']);
        $this->setState($array['state']);
        $this->setStreet1($array['street1']);
        $this->setZipcode($array['zipcode']);
        return $this;
    }

    /**
     * Set Street1
     *
     * @param string $street
     * @return EntityInterface
     */
    public function setStreet1(string $street): EntityInterface
    {
        $this->street1 = $street;
        return $this;
    }

    /**
     * Set State
     *
     * @param string $state
     * @return EntityInterface
     * @throws AddressEntityException
     */
    public function setState(string $state): EntityInterface
    {
        if (true === is_numeric($state)) {
            $region = $this->regionFactory->create()->load($state);
            $state = $region->getCode();
            if (true === empty($state)) {
                throw new AddressEntityException(
                    AddressEntityException::STATE_FROM_REGION_ID_NOT_FOUND_ERROR_MESSAGE,
                    AddressEntityException::STATE_FROM_REGION_ID_NOT_FOUND_ERROR_CODE
                );
            }
        }

        $this->state = $state;
        return $this;
    }

    /**
     * Set City
     *
     * @param string $city
     * @return EntityInterface
     */
    public function setCity(string $city): EntityInterface
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Set Zipcode
     *
     * @param string $zipcode
     * @return EntityInterface
     */
    public function setZipcode(string $zipcode): EntityInterface
    {
        $this->zipcode = $zipcode;
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
            'street1' => $this->street1,
            'state' => $this->state,
            'city' => $this->city,
            'zipcode' => $this->zipcode
        ];
    }
}
