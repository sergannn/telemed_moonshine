<?php

namespace App\GraphQL\Types;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class FlexibleDateScalar extends ScalarType
{
    public $name = 'FlexibleDate';
    public $description = 'A date string that accepts multiple formats (YYYY-MM-DD or DD.MM.YYYY) and converts to YYYY-MM-DD';

    public function serialize($value)
    {
        // When outputting data, ensure it's in YYYY-MM-DD format
        if (!$this->isValidDate($value)) {
            throw new Error("Cannot represent value as date: " . Utils::printSafe($value));
        }

        return $this->normalizeDate($value);
    }

    public function parseValue($value)
    {
        // When parsing input values, accept multiple formats but convert to YYYY-MM-DD
        if (!$this->isValidDate($value)) {
            throw new Error("Cannot represent value as date: " . Utils::printSafe($value));
        }

        return $this->normalizeDate($value);
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }

        $value = $valueNode->value;
        if (!$this->isValidDate($value)) {
            throw new Error("Not a valid date: " . Utils::printSafe($value), [$valueNode]);
        }

        return $this->normalizeDate($value);
    }

    /**
     * Check if the value is a valid date in either YYYY-MM-DD or DD.MM.YYYY format
     */
    private function isValidDate($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        // Check for YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return checkdate(
                (int) substr($value, 5, 2),
                (int) substr($value, 8, 2),
                (int) substr($value, 0, 4)
            );
        }

        // Check for DD.MM.YYYY format
        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
            return checkdate(
                (int) substr($value, 3, 2),
                (int) substr($value, 0, 2),
                (int) substr($value, 6, 4)
