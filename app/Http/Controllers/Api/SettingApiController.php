<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    /**
     * Default settings values
     */
    public static function getDefaultSettings(): array
    {
        return [
            'website_name' => 'HMH Hotel Group',
            'website_url' => 'https://www.hmhhotelgroup.com',
            'admin_email' => 'admin@hmhhotelgroup.com',
            'time_zone' => 'Asia/Dubai',
            'default_language' => 'en',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            
            'company_name' => 'Hospitality Management Holding (HMH)',
            'company_registration_number' => 'REG-123456789',
            'company_address' => 'Sheikh Zayed Road, Dubai, United Arab Emirates',
            'company_country' => 'United Arab Emirates',
            'company_phone' => '+971 4 123 4567',
            'company_email' => 'info@hmhhotelgroup.com',
            'company_logo' => null,
            'footer_logo' => null,
            'company_favicon' => null,
            
            'homepage_title' => 'HMH Hotel Group - Premium Hospitality',
            'homepage_meta_description' => 'Experience luxury and comfort across the Middle East with HMH Hotel Group.',
            'default_banner_image' => null,
            'maintenance_mode' => false,
            'enable_search' => true,
            
            'head_office_address' => 'Sheikh Zayed Road, P.O. Box 12345, Dubai, UAE',
            'contact_phone_number' => '+971 4 123 4567',
            'contact_email_address' => 'contact@hmhhotelgroup.com',
            'customer_support_email' => 'support@hmhhotelgroup.com',
            'google_maps_url' => 'https://maps.google.com/?q=HMH+Hotel+Group',
            
            'facebook_url' => 'https://facebook.com/hmhhotelgroup',
            'instagram_url' => 'https://instagram.com/hmhhotelgroup',
            'linkedin_url' => 'https://linkedin.com/company/hmhhotelgroup',
            'twitter_url' => 'https://twitter.com/hmhhotelgroup',
            'youtube_url' => 'https://youtube.com/hmhhotelgroup',
            'threads_url' => 'https://threads.net/@hmhhotelgroup',
            'tiktok_url' => 'https://www.tiktok.com/@operagrandhoteldubai',
            'google_map_url' => 'https://maps.google.com/?q=Opera+Grand+Hotel+Dubai',
            
            'our_brands_title' => 'OUR BRANDS',
            'our_brands_background' => null,
            
            'default_meta_title' => 'HMH Hotel Group',
            'default_meta_description' => 'Official website of HMH Hotel Group.',
            'default_meta_keywords' => 'hotels, dubai, uae, luxury, hmh',
            'robots_meta_tag' => 'index, follow',
            'google_analytics_id' => 'G-ABC123XYZ',
            'google_tag_manager_id' => 'GTM-ABCDEF',
        ];
    }

    /**
     * Get all settings merged with defaults.
     */
    public function index(Request $request): JsonResponse
    {
        $defaults = self::getDefaultSettings();
        $stored = Setting::getAll();

        $merged = array_merge($defaults, $stored);

        // Convert booleans
        $merged['maintenance_mode'] = filter_var($merged['maintenance_mode'], FILTER_VALIDATE_BOOLEAN);
        $merged['enable_search'] = filter_var($merged['enable_search'], FILTER_VALIDATE_BOOLEAN);

        // Convenient social object
        $merged['social'] = [
            'facebook' => $merged['facebook_url'] ?? null,
            'instagram' => $merged['instagram_url'] ?? null,
            'linkedin' => $merged['linkedin_url'] ?? null,
            'twitter' => $merged['twitter_url'] ?? null,
            'youtube' => $merged['youtube_url'] ?? null,
            'threads' => $merged['threads_url'] ?? null,
            'tiktok' => $merged['tiktok_url'] ?? null,
            'google_map' => $merged['google_map_url'] ?? ($merged['google_maps_url'] ?? null),
        ];

        // Format image URLs
        if (!empty($merged['company_logo'])) {
            $merged['company_logo_url'] = url('/uploads/' . ltrim($merged['company_logo'], '/'));
        }
        if (!empty($merged['footer_logo'])) {
            $merged['footer_logo_url'] = url('/uploads/' . ltrim($merged['footer_logo'], '/'));
        }
        if (!empty($merged['company_favicon'])) {
            $merged['company_favicon_url'] = url('/uploads/' . ltrim($merged['company_favicon'], '/'));
        }
        if (!empty($merged['default_banner_image'])) {
            $merged['default_banner_image_url'] = url('/uploads/' . ltrim($merged['default_banner_image'], '/'));
        }
        if (!empty($merged['our_brands_background'])) {
            $merged['our_brands_background_url'] = url('/uploads/' . ltrim($merged['our_brands_background'], '/'));
        }

        return response()->json([
            'success' => true,
            'data' => $merged,
        ]);
    }
}
