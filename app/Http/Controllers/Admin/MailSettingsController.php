<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MailSettingsRequest;
use App\Models\Setting;
use App\Support\MailSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class MailSettingsController extends Controller
{
    /**
     * Show the email settings form.
     */
    public function edit(): View
    {
        $settings = Setting::values();
        unset($settings['smtp_password']);

        return view('admin.mail.edit', [
            'settings' => $settings,
            'hasPassword' => (bool) Setting::get('smtp_password'),
            'encryptions' => MailSettings::ENCRYPTIONS,
            'notifyDefault' => Setting::get('contact_email'),
            'notifyEnabled' => MailSettings::notificationsEnabled(),
        ]);
    }

    /**
     * Save the email settings. A blank password keeps the saved one.
     */
    public function update(MailSettingsRequest $request): RedirectResponse
    {
        $values = [
            'smtp_enabled' => $request->boolean('smtp_enabled') ? '1' : '0',
            'smtp_host' => $request->input('smtp_host'),
            'smtp_port' => $request->input('smtp_port'),
            'smtp_encryption' => $request->input('smtp_encryption'),
            'smtp_username' => $request->input('smtp_username'),
            'mail_from_address' => $request->input('mail_from_address'),
            'mail_from_name' => $request->input('mail_from_name'),
            'lead_notify_enabled' => $request->boolean('lead_notify_enabled') ? '1' : '0',
            'lead_notify_to' => $request->input('lead_notify_to'),
        ];

        if ($request->boolean('clear_smtp_password')) {
            $values['smtp_password'] = null;
        } elseif (filled($request->input('smtp_password'))) {
            $values['smtp_password'] = Crypt::encryptString($request->input('smtp_password'));
        }

        Setting::setMany($values);

        return redirect()->route('admin.mail.edit')->with('status', 'Email settings saved.');
    }

    /**
     * Send a test email using the saved settings, and report the outcome.
     */
    public function test(Request $request): RedirectResponse
    {
        $data = $request->validate(['test_email' => ['required', 'email', 'max:255']]);

        $usingSmtp = MailSettings::apply();

        try {
            Mail::raw(
                'This is a test email from '.(Setting::get('site_name') ?: config('app.name')).'. If you can read it, your email settings work.',
                fn ($message) => $message->to($data['test_email'])->subject('Test email'),
            );
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('admin.mail.edit')
                ->withInput()
                ->with('mail_error', 'The test email could not be sent: '.$e->getMessage());
        }

        return redirect()->route('admin.mail.edit')->with('status', $usingSmtp
            ? 'Test email sent to '.$data['test_email'].' through your SMTP server.'
            : 'Test email handed to the default mailer ('.config('mail.default').') because SMTP is off. Turn SMTP on to send through your own server.');
    }
}
