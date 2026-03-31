<?php

namespace App\Utils;

class Validator {
    public static function validate(array $data, array $rules): array {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $rule);

            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field] = "$field is required";
                } elseif (str_starts_with($r, 'min:')) {
                    $min = (int)substr($r, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "$field must be at least $min characters";
                    }
                } elseif (str_starts_with($r, 'max:')) {
                    $max = (int)substr($r, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "$field must be at most $max characters";
                    }
                } elseif ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "$field must be a valid email";
                } elseif ($r === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = "$field must be numeric";
                }
            }
        }

        return $errors;
    }
}