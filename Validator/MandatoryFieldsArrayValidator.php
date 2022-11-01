<?php

namespace WeGetFinancing\Checkout\Validator;

use WeGetFinancing\Checkout\Entity\ErrorEntity;
use WeGetFinancing\Checkout\Exception\FunnelGeneratorRequestException;

class MandatoryFieldsArrayValidator implements MandatoryFieldsArrayValidatorInterface
{
    /**
     * @var ErrorEntity[]
     */
    protected array $validationErrors = [];

    public function validate(array $mandatoryFields, array $array): bool
    {
        foreach ($mandatoryFields as $mandatoryField) {
            $this->validateMandatoryField($array, $mandatoryField);
        }

        return empty($this->validationErrors);
    }

    public function getValidationErrors(): array
    {
        /** @var $error ErrorEntity */
        return array_map(function ($error) {
                return $error->toArray();
            },
            $this->validationErrors
        );
    }

    protected function validateMandatoryField(array $array, string $fieldName): void
    {
        if (false === isset($array[$fieldName]) || true === empty($array[$fieldName])) {
            $this->validationErrors[] = new ErrorEntity([
                'field' => $fieldName,
                'message' => str_replace(
                    FunnelGeneratorRequestException::FIELD_CONST,
                    $fieldName,
                    FunnelGeneratorRequestException::EMPTY_MANDATORY_FIELD_ERROR_MESSAGE
                )
            ]);
        }
    }
}
