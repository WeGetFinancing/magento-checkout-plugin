<?php

namespace WeGetFinancing\Checkout\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class GeneralResponseValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = $validationSubject['response'];

        $isValid = true;
        $errorMessages = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($response);
            if (!$validationResult[0]) {
                $isValid = $validationResult[0];
                if (false === in_array($validationResult[1], $errorMessages)) {
                    $errorMessages[] = $validationResult[1];
                }
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }

    /**
     * Get Response Validators
     *
     * @return mixed
     */
    private function getResponseValidators(): array
    {
        return [
            function ($response) {
                return [
                    isset($response['messages']['resultCode']) && 'Ok' === $response['messages']['resultCode'],
                    [$response['messages']['message'][0]['text'] ?? __('WeGetFinancing error response')]
                ];
            }
        ];
    }
}
