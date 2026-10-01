<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Social media profiles that can be linked, as setting key => label.
     */
    public const SOCIAL = [
        'facebook_url' => 'Facebook',
        'instagram_url' => 'Instagram',
        'twitter_url' => 'X (Twitter)',
        'youtube_url' => 'YouTube',
        'linkedin_url' => 'LinkedIn',
        'pinterest_url' => 'Pinterest',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],

            'site_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about_content' => ['nullable', 'string', 'max:5000'],
            'footer_text' => ['nullable', 'string', 'max:1000'],

            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_phone_alt' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'map_embed' => ['nullable', 'string', 'max:5000'],

            'head_scripts' => ['nullable', 'string', 'max:20000'],
            'body_scripts' => ['nullable', 'string', 'max:20000'],
        ];

        foreach (array_keys(self::SOCIAL) as $key) {
            $rules[$key] = ['nullable', 'url', 'max:255'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.mimes' => 'The logo must be a JPG, PNG, WebP or SVG file.',
            'logo.max' => 'The logo may not be larger than 2 MB.',
            '*.url' => 'Enter a full link starting with https://',
        ];
    }
}
