<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * SMTP and notification settings managed from the admin panel.
 *
 * When SMTP is enabled there, its values replace the mailer configured in .env
 * for outgoing mail. The password is stored encrypted.
 */
class MailSettings
{
    public const ENCRYPTIONS = [
        'tls' => 'TLS / STARTTLS (usually port 587)',
        'ssl' => 'SSL (usually port 465)',
        'none' => 'None (usually port 25)',
    ];

    /**
     * Point Laravel's mailer at the SMTP server saved in the admin panel.
     * Returns false, and leaves the .env mailer alone, when it is switched off
     * or incomplete.
     */
    public static function apply(): bool
    {
        $settings = Setting::values();

        if (empty($settings['smtp_enabled']) || empty($settings['smtp_host'])) {
            return false;
        }

        $password = null;
        if (! empty($settings['smtp_password'])) {
            try {
                $password = Crypt::decryptString($settings['smtp_password']);
            } catch (Throwable) {
                $password = null;
            }
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.scheme' => ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'smtps' : 'smtp',
            'mail.mailers.smtp.host' => $settings['smtp_host'],
            'mail.mailers.smtp.port' => (int) (($settings['smtp_port'] ?? null) ?: 587),
            'mail.mailers.smtp.username' => ($settings['smtp_username'] ?? null) ?: null,
            'mail.mailers.smtp.password' => $password,
        ]);

        if (! empty($settings['mail_from_address'])) {
            config([
                'mail.from.address' => $settings['mail_from_address'],
                'mail.from.name' => ($settings['mail_from_name'] ?? null) ?: ($settings['site_name'] ?? config('app.name')),
            ]);
        }

        // Drop any mailer built earlier in this request from the old config.
        Mail::purge();

        return true;
    }

    /**
     * Who receives new-lead notifications: the addresses saved for it, or the
     * contact email from Settings.
     *
     * @return list<string>
     */
    public static function recipients(): array
    {
        $raw = Setting::get('lead_notify_to') ?: Setting::get('contact_email') ?: '';

        return array_values(array_filter(
            array_map('trim', preg_split('/[,;\s]+/', $raw)),
            fn (string $address) => filter_var($address, FILTER_VALIDATE_EMAIL),
        ));
    }

    /**
     * Whether new-lead notifications are switched on (they are by default).
     */
    public static function notificationsEnabled(): bool
    {
        return Setting::get('lead_notify_enabled', '1') === '1';
    }
}
