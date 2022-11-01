<?php

namespace WeGetFinancing\Checkout\Entity\Response;

use WeGetFinancing\Checkout\Entity\AbstractEntity;
use WeGetFinancing\Checkout\Entity\EntityInterface;
use WeGetFinancing\Checkout\Exception\JsonResponseException;

class JsonResponse extends AbstractEntity
{
    const TYPE_SUCCESS = 'SUCCESS';
    const TYPE_ERROR = 'ERROR';

    /**
     * @var string(self::TYPE_SUCCESS | self::TYPE_ERROR)
     */
    protected $type;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $type
     * @return $this
     * @throws JsonResponseException
     */
    public function setType(string $type)
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
     * @param array $messages
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
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
