<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeContentRequest;
use App\Models\Setting;
use App\Services\ImageService;
use App\Support\HomeContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeContentController extends Controller
{
    /**
     * Show the page content form.
     */
    public function edit(): View
    {
        $settings = Setting::values();
        $siteName = $settings['site_name'] ?? config('app.name');

        return view('admin.content.edit', [
            'sections' => HomeContent::sections(),
            'saved' => HomeContent::saved($settings),
            'defaults' => collect(HomeContent::textKeys())
                ->mapWithKeys(fn (string $key) => [$key => HomeContent::default($key, $siteName)])
                ->all(),
            'images' => HomeContent::images($settings),
        ]);
    }

    /**
     * Save the page content. Empty text falls back to the default copy.
     */
    public function update(HomeContentRequest $request, ImageService $images): RedirectResponse
    {
        $values = [];

        foreach (HomeContent::textKeys() as $key) {
            $text = trim((string) $request->input($key));
            $values[HomeContent::settingKey($key)] = $text !== '' ? $text : null;
        }

        foreach (HomeContent::imageKeys() as $key) {
            $settingKey = HomeContent::settingKey($key);

            if ($request->hasFile($key)) {
                $images->delete(Setting::get($settingKey));
                $values[$settingKey] = $request->file($key)->store('home', 'uploads');
            } elseif ($request->boolean('remove_'.$key)) {
                $images->delete(Setting::get($settingKey));
                $values[$settingKey] = null;
            }
        }

        Setting::setMany($values);

        return redirect()->route('admin.content.edit')->with('status', 'Page content saved.');
    }
}
