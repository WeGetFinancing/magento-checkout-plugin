<?php

namespace WeGetFinancing\Checkout\Entity;

use Magento\Directory\Model\RegionFactory;
use WeGetFinancing\Checkout\Exception\AddressEntityException;

class AddressEntity implements EntityInterface
{
    protected string $street1;

    protected string $state;

    protected string $city;

    protected string $zipcode;

    private RegionFactory $regionFactory;

    public function __construct(
        RegionFactory $regionFactory
    ) {
        $this->regionFactory = $regionFactory;
    }

    /**
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

    public function setStreet1(string $street): EntityInterface
    {
        $this->street1 = $street;
        return $this;
    }

    /**
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

    public function setCity(string $city): EntityInterface
    {
        $this->city = $city;
        return $this;
    }

    public function setZipcode(string $zipcode): EntityInterface
    {
        $this->zipcode = $zipcode;
        return $this;
    }

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
