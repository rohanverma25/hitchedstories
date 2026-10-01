<?php

namespace App\Support;

use App\Http\Requests\Admin\SettingsRequest;
use App\Models\Setting;
use Illuminate\Support\HtmlString;

/**
 * The editable text and images of the single-page site.
 *
 * Every field has a default (the original copy), so the site reads well before
 * anything is saved and clearing a field in the admin panel restores it.
 */
class HomeContent
{
    /**
     * Editable sections: id => [label, hint, fields]. Each field is
     * key => [label, type (text|textarea|image), default, default image alt].
     * Setting keys are stored as "home_{key}".
     *
     * @return array<string, array{label: string, hint: ?string, fields: array<string, array{0: string, 1: string, 2: string}>}>
     */
    public static function sections(): array
    {
        return [
            'hero' => ['label' => 'Hero', 'hint' => 'The full-screen opening of the page.', 'fields' => [
                'hero_image' => ['Background image', 'image', 'hero-light.jpg'],
                'hero_title' => ['Heading, after the site name', 'text', 'Photography'],
                'hero_title_accent' => ['Heading, second part', 'text', '& Films'],
                'hero_tagline_1' => ['Tagline, first line', 'text', '“Your love story, told'],
                'hero_tagline_2' => ['Tagline, second line', 'text', 'the way it felt.”'],
            ]],
            'intro' => ['label' => 'Introduction', 'hint' => null, 'fields' => [
                'intro_eyebrow' => ['Small label', 'text', 'Weddings · Couples · Portraits'],
                'intro_heading' => ['Heading', 'text', 'Honest, unhurried photographs for *people in love*'],
                'intro_text' => ['Text', 'textarea', 'We are a small studio of photographers and filmmakers who believe the best pictures happen between the planned ones. We watch, we wait, and we keep what is real.'],
            ]],
            'portfolio' => ['label' => 'Portfolio', 'hint' => 'Three story cards.', 'fields' => [
                'portfolio_eyebrow' => ['Small label', 'text', 'Portfolio'],
                'portfolio_heading' => ['Heading', 'text', 'Stories we have *told*'],
                'portfolio_1_title' => ['Card 1 title', 'text', 'Weddings'],
                'portfolio_1_caption' => ['Card 1 caption', 'text', 'The whole day, start to finish'],
                'portfolio_1_image' => ['Card 1 image', 'image', 'portfolio-weddings.jpg'],
                'portfolio_2_title' => ['Card 2 title', 'text', 'Couples'],
                'portfolio_2_caption' => ['Card 2 caption', 'text', 'Pre-wedding and engagement'],
                'portfolio_2_image' => ['Card 2 image', 'image', 'portfolio-couples.jpg'],
                'portfolio_3_title' => ['Card 3 title', 'text', 'Details'],
                'portfolio_3_caption' => ['Card 3 caption', 'text', 'The little things you chose'],
                'portfolio_3_image' => ['Card 3 image', 'image', 'portfolio-details.jpg'],
            ]],
            'about' => ['label' => 'About us', 'hint' => null, 'fields' => [
                'about_eyebrow' => ['Small label', 'text', 'About us'],
                'about_heading' => ['Heading', 'text', 'Soft moments, captured *with heart*'],
                'about_text_1' => ['First paragraph', 'textarea', '{name} began with a simple idea: a wedding is not a list of shots, it is a story with people in it. So we spend less time directing and more time paying attention.'],
                'about_text_2' => ['Second paragraph', 'textarea', 'From the nervous laughter before the ceremony to the quiet minute you steal for yourselves, we look for the feeling first and the frame second. The result is a set of photographs and films that still sound like you, years from now.'],
                'about_signature' => ['Signature', 'text', 'With love, the {name} team'],
                'about_button' => ['Button text', 'text', 'Meet us'],
                'about_image' => ['Main image', 'image', 'about.jpg'],
                'about_image_small' => ['Small overlapping image', 'image', 'gallery-03.jpg'],
            ]],
            'headings' => ['label' => 'Section headings', 'hint' => 'Titles above Services, Gallery, Reviews and Journal. Services, gallery photos, reviews and blogs themselves are managed in their own menus.', 'fields' => [
                'services_eyebrow' => ['Services, small label', 'text', 'Services'],
                'services_heading' => ['Services, heading', 'text', 'What we *do*'],
                'gallery_eyebrow' => ['Gallery, small label', 'text', 'Gallery'],
                'gallery_heading' => ['Gallery, heading', 'text', 'A few favourite *frames*'],
                'reviews_eyebrow' => ['Reviews, small label', 'text', 'Kind words'],
                'reviews_heading' => ['Reviews, heading', 'text', 'From our *couples*'],
                'journal_eyebrow' => ['Journal, small label', 'text', 'Journal'],
                'journal_heading' => ['Journal, heading', 'text', 'Notes from *the studio*'],
            ]],
            'quote' => ['label' => 'Quote', 'hint' => null, 'fields' => [
                'quote_text' => ['Quote', 'text', 'We do not pose moments. We wait for them.'],
            ]],
            'cta' => ['label' => 'Call to action', 'hint' => 'The banner above the contact form.', 'fields' => [
                'cta_eyebrow' => ['Small label', 'text', 'Let\'s begin'],
                'cta_heading' => ['Heading', 'text', 'Tell us about *your day*'],
                'cta_button' => ['Button text', 'text', 'Get in touch'],
                'cta_image' => ['Background image', 'image', 'cta.jpg'],
            ]],
            'contact' => ['label' => 'Contact section', 'hint' => 'Email, phone and address come from Settings.', 'fields' => [
                'contact_eyebrow' => ['Small label', 'text', 'Contact'],
                'contact_heading' => ['Heading', 'text', 'Let\'s document *your story*'],
                'contact_text' => ['Text', 'textarea', 'Share a few details and we will write back within two days with availability and a full guide to our collections.'],
                'contact_image' => ['Side image', 'image', 'contact.jpg'],
            ]],
        ];
    }

    /**
     * Every field, flattened: key => [label, type, default].
     *
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function fields(): array
    {
        return array_merge(...array_column(self::sections(), 'fields'));
    }

    /**
     * Keys of the image fields.
     *
     * @return list<string>
     */
    public static function imageKeys(): array
    {
        return array_keys(array_filter(self::fields(), fn (array $field) => $field[1] === 'image'));
    }

    /**
     * Keys of the text and textarea fields.
     *
     * @return list<string>
     */
    public static function textKeys(): array
    {
        return array_keys(array_filter(self::fields(), fn (array $field) => $field[1] !== 'image'));
    }

    /**
     * Setting key a field is stored under.
     */
    public static function settingKey(string $key): string
    {
        return 'home_'.$key;
    }

    /**
     * The default text of a field, with the site name filled in.
     */
    public static function default(string $key, string $siteName): string
    {
        return str_replace('{name}', $siteName, self::fields()[$key][2]);
    }

    /**
     * Text values for the site, falling back to defaults for empty fields.
     *
     * @param  array<string, ?string>  $settings
     * @return array<string, string>
     */
    public static function texts(array $settings, string $siteName): array
    {
        $texts = [];

        foreach (self::textKeys() as $key) {
            $value = trim((string) ($settings[self::settingKey($key)] ?? ''));
            $texts[$key] = $value !== '' ? $value : self::default($key, $siteName);
        }

        return $texts;
    }

    /**
     * Image URLs for the site: the uploaded file, or the bundled default.
     *
     * @param  array<string, ?string>  $settings
     * @return array<string, string>
     */
    public static function images(array $settings): array
    {
        $images = [];

        foreach (self::imageKeys() as $key) {
            $uploaded = $settings[self::settingKey($key)] ?? null;
            $images[$key] = $uploaded
                ? asset('uploads/'.$uploaded)
                : asset('images/site/'.self::fields()[$key][2]);
        }

        return $images;
    }

    /**
     * Escape a heading and turn *word* into <em>word</em>.
     */
    public static function emphasise(string $text): HtmlString
    {
        return new HtmlString(preg_replace('/\*(.+?)\*/u', '<em>$1</em>', e($text)));
    }

    /**
     * Saved values for the admin form, without defaults, so empty stays empty.
     *
     * @param  array<string, ?string>  $settings
     * @return array<string, ?string>
     */
    public static function saved(array $settings): array
    {
        $saved = [];

        foreach (array_keys(self::fields()) as $key) {
            $saved[$key] = $settings[self::settingKey($key)] ?? null;
        }

        return $saved;
    }

    /**
     * Turn the src of a pasted Google Maps <iframe> into a safe, self-built embed URL.
     */
    public static function mapSrc(?string $embed): ?string
    {
        if (! $embed || ! preg_match('/src\s*=\s*["\'](https:\/\/[^"\']+)["\']/i', $embed, $match)) {
            return null;
        }

        return html_entity_decode($match[1]);
    }

    /**
     * Shared site-wide values: identity, contact details, social links, scripts.
     *
     * @return array<string, mixed>
     */
    public static function site(): array
    {
        $settings = Setting::values();
        $name = trim((string) ($settings['site_name'] ?? '')) ?: config('app.name');

        return [
            'name' => $name,
            'tagline' => trim((string) ($settings['tagline'] ?? '')) ?: 'Wedding Photography & Films',
            'footerLine' => trim((string) ($settings['about_content'] ?? '')) ?: 'Honest, unhurried photographs and films for people in love.',
            'footerText' => trim((string) ($settings['footer_text'] ?? '')) ?: '© '.date('Y').' '.$name.'. All rights reserved.',
            'logo' => Setting::logoUrl(),
            'email' => $settings['contact_email'] ?? null,
            'phone' => $settings['contact_phone'] ?? null,
            'phoneAlt' => $settings['contact_phone_alt'] ?? null,
            'whatsapp' => $settings['whatsapp_number'] ?? null,
            'address' => $settings['address'] ?? null,
            'hours' => $settings['working_hours'] ?? null,
            'mapSrc' => self::mapSrc($settings['map_embed'] ?? null),
            'social' => array_filter(
                array_intersect_key($settings, SettingsRequest::SOCIAL),
            ),
            'headScripts' => $settings['head_scripts'] ?? null,
            'bodyScripts' => $settings['body_scripts'] ?? null,
            'content' => self::texts($settings, $name),
            'images' => self::images($settings),
        ];
    }
}
