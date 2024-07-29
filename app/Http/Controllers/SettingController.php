<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function submitApiKeysForm(Request $request)
    {
        return response()->view('partials.form-response', [
            'success' => true,
            'message' => 'Form submitted successfully!',
        ]);
        
        // Validate the form data
        $validatedData = $request->validate([
            'field1' => 'required',
            'field2' => 'required|email',
            // Add more validation rules as needed
        ]);



        return response()->view('partials.form-response', [
            'success' => true,
            'message' => 'Form submitted successfully!',
        ]);
    }
}
