<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Send the visitor back to the contact section on validation errors.
     */
    protected function getRedirectUrl(): string
    {
        return route('home').'#contact';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'venue' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:3000'],
            // Honeypot: hidden from people, filled in by bots.
            'website' => ['nullable', 'max:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your names.',
            'email.required' => 'Please enter your email so we can reply.',
            'email.email' => 'That email address does not look right.',
            'date.after_or_equal' => 'The wedding date cannot be in the past.',
            'website.max' => 'Something went wrong. Please try again.',
        ];
    }
}
