<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Sanitize monetary input fields by stripping thousand separators (dots/commas).
     * This ensures formatted values like "100.000.000" are converted to "100000000"
     * before validation and storage.
     */
    protected function sanitizeMoneyFields(Request $request, array $fields): void
    {
        $merged = [];
        foreach ($fields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $merged[$field] = preg_replace('/[^\d]/', '', $request->input($field)) ?: 0;
            }
        }
        if (!empty($merged)) {
            $request->merge($merged);
        }
    }
}
