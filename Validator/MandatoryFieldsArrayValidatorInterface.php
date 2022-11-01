<?php

namespace WeGetFinancing\Checkout\Validator;

interface MandatoryFieldsArrayValidatorInterface
{
    /**
     * @param string[] $mandatoryFields
     * @param array $array = [
     *      field => data
     * ]
     * @return bool
     */
    public function validate(array $mandatoryFields, array $array): bool;

    public function getValidationErrors(): array;
}
