<?php

declare(strict_types=1);

/**
 * env.php — copy of env.example.php with your real credentials.
 *
 */

return function (array $settings): array {

    // ── Database ────────────────────────────────────────────────────────────
    $settings['db']['username'] = 'root';
    $settings['db']['database'] = 'Mix-Max';
    $settings['db']['password'] = 'root';

    // ── Twilio Verify ───────────────────────────────────────────────────────
    // Get these from https://console.twilio.com
    //   Account SID + Auth Token: top of the Console dashboard
    //   Verify Service SID: Verify → Services → create a service → copy the SID
    $settings['twilio']['account_sid'] = 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    $settings['twilio']['auth_token'] = 'your_auth_token_here';
    $settings['twilio']['verify_service_sid'] = 'VAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

    // —— Cloudinary ———————————————————————————————
    // Get these from https://console.cloudinary.com
    // Dashboard → API Keys
    $settings['cloudinary']['cloud_name'] = 'your_cloud_name';
    $settings['cloudinary']['api_key'] = 'your_api_key';
    $settings['cloudinary']['api_secret'] = 'your_api_secret';

    return $settings;
};