<?php

namespace App\Http\Requests\Admin;

use App\Support\HomeContent;
use Illuminate\Foundation\Http\FormRequest;

class HomeContentRequest extends FormRequest
{
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
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (HomeContent::fields() as $key => [, $type]) {
            $rules[$key] = match ($type) {
                'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'textarea' => ['nullable', 'string', 'max:2000'],
                default => ['nullable', 'string', 'max:255'],
            };
            if ($type === 'image') {
                $rules['remove_'.$key] = ['nullable', 'boolean'];
            }
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
            '*.mimes' => 'The image must be a JPG, PNG or WebP file.',
            '*.max' => 'This field is too long or the image is larger than 5 MB.',
        ];
    }
}
