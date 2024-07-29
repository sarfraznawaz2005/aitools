<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'llm_type' => 'required',
            'base_url' => 'required_if:llm_type,ollama',
            'api_key' => 'required|unique:api_keys',
            'name' => 'required|unique:api_keys',
        ]);

        if ($validator->fails()) {
            return response()->view('partials.response', [
                'success' => false,
                'message' => 'Validation failed!',
                'errors' => $validator->errors(),
            ]);
        }

        // Save the form data to the database
        ApiKey::create($validator->validated());

        return response()->view('partials.response', [
            'success' => true,
            'message' => 'API key saved successfully!',
        ]);
    }
}
