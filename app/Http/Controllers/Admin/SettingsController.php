<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings form.
     */
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => Setting::values(),
            'logoUrl' => Setting::logoUrl(),
            'social' => SettingsRequest::SOCIAL,
        ]);
    }

    /**
     * Save the settings.
     */
    public function update(SettingsRequest $request): RedirectResponse
    {
        $values = Arr::except($request->validated(), ['logo', 'remove_logo']);

        if ($request->hasFile('logo')) {
            $this->deleteLogo();
            $values['logo'] = $request->file('logo')->store('settings', 'uploads');
        } elseif ($request->boolean('remove_logo')) {
            $this->deleteLogo();
            $values['logo'] = null;
        }

        Setting::setMany($values);

        return redirect()->route('admin.settings.edit')->with('status', 'Settings saved.');
    }

    private function deleteLogo(): void
    {
        if ($logo = Setting::get('logo')) {
            Storage::disk('uploads')->delete($logo);
        }
    }
}
