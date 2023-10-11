<?php

namespace WeGetFinancing\Checkout\Validator;

interface MandatoryFieldsArrayValidatorInterface
{
    /**
     * Validate
     *
     * @param string[] $mandatoryFields
     * @param array $array = [
     *      field => data
     * ]
     * @return bool
     */
    public function validate(array $mandatoryFields, array $array): bool;

    /**
     * Get Validation Errors
     *
     * @return array
     */
    public function getValidationErrors(): array;
}
