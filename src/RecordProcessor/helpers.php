<?php

namespace RodrigoPedra\RecordProcessor;

if (! function_exists('value_or_null')) {
    function value_or_null(mixed $value): mixed
    {
        $value = value($value);

        if (is_object($value)) {
            return $value;
        }

        if (is_array($value)) {
            return empty($value) ? null : $value;
        }

        $value = trim($value);

        if (empty($value) || ! $value) {
            return null;
        }

        return $value;
    }
}

if (! function_exists('is_associative_array')) {
    function is_associative_array(array $value): bool
    {
        return is_array($value) && array_diff_key($value, array_keys(array_keys($value)));
    }
}
