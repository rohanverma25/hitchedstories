<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryUploadRequest extends FormRequest
{
    /**
     * Maximum number of images accepted in one upload.
     */
    public const MAX_FILES = 20;

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
        return [
            'images' => ['required', 'array', 'min:1', 'max:'.self::MAX_FILES],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
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
            'images.required' => 'Choose at least one image to upload.',
            'images.max' => 'You can upload up to '.self::MAX_FILES.' images at a time.',
            'images.*.image' => 'Image #:position is not a valid image.',
            'images.*.mimes' => 'Image #:position must be a JPG, PNG, WebP or GIF.',
            'images.*.max' => 'Image #:position is larger than 5 MB.',
            'images.*.dimensions' => 'Image #:position is larger than 6000 × 6000 pixels.',
        ];
    }
}
