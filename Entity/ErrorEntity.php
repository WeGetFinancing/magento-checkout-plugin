<?php

namespace WeGetFinancing\Checkout\Entity;

use WeGetFinancing\Checkout\Exception\ErrorEntityException;

class ErrorEntity extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $field;

    /**
     * @var string
     */
    protected string $message;

    /**
     * Get Field
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set Field
     *
     * @param string $field
     * @return EntityInterface
     */
    public function setField(string $field): EntityInterface
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Set Message
     *
     * @param string $message
     * @return EntityInterface
     */
    public function setMessage(string $message): EntityInterface
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Init From Array
     *
     * @param array $array
     * @return EntityInterface
     * @throws ErrorEntityException
     */
    public function initFromArray(array $array): EntityInterface
    {
        if ((false === isset($array['field']) || true === empty($array['field'])) ||
            (false === isset($array['message']) || true === empty($array['message']))) {
            throw new ErrorEntityException(
                ErrorEntityException::INVALID_INIT_DATA_ERROR_MESSAGE,
                ErrorEntityException::INVALID_INIT_DATA_ERROR_CODE
            );
        }

        $this->setField($array['field']);
        $this->setMessage($array['message']);

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
            'field' => $this->getField(),
            'message' => $this->getMessage()
        ];
    }
}
