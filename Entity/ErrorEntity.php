<?php

namespace WeGetFinancing\Checkout\Entity;

use WeGetFinancing\Checkout\Exception\ErrorEntityException;

class ErrorEntity extends AbstractEntity
{
    protected string $field;

    protected string $message;

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setField(string $field): EntityInterface
    {
        $this->field = $field;
        return $this;
    }

    public function setMessage(string $message): EntityInterface
    {
        $this->message = $message;
        return $this;
    }

    /**
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

    public function toArray(): array
    {
        return [
            'field' => $this->getField(),
            'message' => $this->getMessage()
        ];
    }

}
