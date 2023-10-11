<?php

namespace WeGetFinancing\Checkout\Entity\Response;

use WeGetFinancing\Checkout\Entity\AbstractEntity;
use WeGetFinancing\Checkout\Entity\EntityInterface;
use WeGetFinancing\Checkout\Exception\JsonResponseException;

class JsonResponse extends AbstractEntity
{
    public const TYPE_SUCCESS = 'SUCCESS';
    public const TYPE_ERROR = 'ERROR';

    /**
     * @var string(self::TYPE_SUCCESS | self::TYPE_ERROR)
     */
    protected string $type;

    /**
     * @var array
     */
    protected array $messages = [];

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * Set Type
     *
     * @param string $type
     * @return $this
     * @throws JsonResponseException
     */
    public function setType(string $type): static
    {
        if (self::TYPE_ERROR === $type || self::TYPE_SUCCESS === $type) {
            $this->type = $type;
            return $this;
        }

        throw new JsonResponseException(
            JsonResponseException::INVALID_TYPE_ERROR_MESSAGE,
            JsonResponseException::INVALID_TYPE_ERROR_CODE
        );
    }

    /**
     * Set Message
     *
     * @param array $messages
     * @return $this
     */
    public function setMessages(array $messages): static
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Set Data
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get Messages
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Init From Array
     *
     * @param array $array
     * @return JsonResponse
     * @throws JsonResponseException
     */
    public function initFromArray(array $array): EntityInterface
    {
        if (false === array_key_exists('type', $array)) {
            throw new JsonResponseException(
                JsonResponseException::INIT_WITHOUT_TYPE_ERROR_MESSAGE,
                JsonResponseException::INIT_WITHOUT_TYPE_ERROR_CODE
            );
        }

        $this->setType($array['type']);

        if (true === array_key_exists('messages', $array)) {
            $this->setMessages($array['messages']);
        }

        if (true === array_key_exists('data', $array)) {
            $this->setData($array['data']);
        }

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
            'type' => $this->getType(),
            'messages' => $this->getMessages(),
            'data' => $this->getData()
        ];
    }
}
