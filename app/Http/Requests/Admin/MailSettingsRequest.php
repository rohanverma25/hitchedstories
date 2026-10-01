<?php

namespace App\Http\Requests\Admin;

use App\Support\MailSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MailSettingsRequest extends FormRequest
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
        $smtp = $this->boolean('smtp_enabled');

        return [
            'smtp_enabled' => ['nullable', 'boolean'],
            'smtp_host' => [Rule::requiredIf($smtp), 'nullable', 'string', 'max:255'],
            'smtp_port' => [Rule::requiredIf($smtp), 'nullable', 'integer', 'between:1,65535'],
            'smtp_encryption' => ['required', Rule::in(array_keys(MailSettings::ENCRYPTIONS))],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:500'],
            'clear_smtp_password' => ['nullable', 'boolean'],
            'mail_from_address' => [Rule::requiredIf($smtp), 'nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'lead_notify_enabled' => ['nullable', 'boolean'],
            'lead_notify_to' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail) {
                foreach (preg_split('/[,;\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) as $address) {
                    if (! filter_var($address, FILTER_VALIDATE_EMAIL)) {
                        $fail("\"{$address}\" is not a valid email address.");
                    }
                }
            }],
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
            'smtp_host.required_if' => 'Enter the SMTP host to turn SMTP on.',
            'smtp_port.required_if' => 'Enter the SMTP port to turn SMTP on.',
            'mail_from_address.required_if' => 'Enter the sender address to turn SMTP on.',
        ];
    }
}
