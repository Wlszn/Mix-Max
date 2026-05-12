<?php

declare(strict_types=1);

namespace App\Domain\Services;

use RuntimeException;

/**
 * Wraps the Twilio Verify v2 REST API using PHP's built-in cURL.
 * No Twilio SDK composer package required.
 *
 * Required env.php entries:
 *   $settings['twilio']['account_sid']      = 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
 *   $settings['twilio']['auth_token']       = 'your_auth_token';
 *   $settings['twilio']['verify_service_sid'] = 'VAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
 */
class TwilioVerifyService extends BaseService
{
    private string $accountSid;
    private string $authToken;
    private string $serviceSid;
    private string $baseUrl;

    public function __construct(string $accountSid, string $authToken, string $serviceSid)
    {
        if (empty($accountSid) || empty($authToken) || empty($serviceSid)) {
            throw new RuntimeException(
                'Twilio credentials are missing. Please set twilio.account_sid, ' .
                'twilio.auth_token, and twilio.verify_service_sid in config/env.php.'
            );
        }

        $this->accountSid = $accountSid;
        $this->authToken  = $authToken;
        $this->serviceSid = $serviceSid;
        $this->baseUrl    = "https://verify.twilio.com/v2/Services/{$this->serviceSid}";
    }

    // ─── Send OTP ──────────────────────────────────────────────────────────────

    /**
     * Send an OTP SMS to the given phone number.
     *
     * @param  string $phoneNumber E.164 format, e.g. "+15141234567"
     * @return bool   true if Twilio accepted the request
     * @throws RuntimeException on HTTP or API error
     */
    public function sendOtp(string $phoneNumber): bool
    {
        $response = $this->post('/Verifications', [
            'To'      => $phoneNumber,
            'Channel' => 'sms',
        ]);

        $status = $response['status'] ?? '';
        if ($status !== 'pending') {
            throw new RuntimeException("Twilio returned unexpected status: '{$status}'");
        }
        return true;
    }

    // ─── Verify OTP ────────────────────────────────────────────────────────────

    /**
     * Check the OTP code submitted by the user.
     *
     * @param  string $phoneNumber E.164 format
     * @param  string $code        The 6-digit code entered by the user
     * @return bool   true if code is correct and not expired
     */
    public function verifyOtp(string $phoneNumber, string $code): bool
    {
        $response = $this->post('/VerificationCheck', [
            'To'   => $phoneNumber,
            'Code' => $code,
        ]);

        return ($response['status'] ?? '') === 'approved';
    }

    // ─── Internal HTTP helper ──────────────────────────────────────────────────

    private function post(string $endpoint, array $params): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_USERPWD        => "{$this->accountSid}:{$this->authToken}",
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            throw new RuntimeException("Twilio cURL error: {$curlErr}");
        }

        $data = json_decode((string) $body, true);

        if ($httpCode >= 400) {
            $msg = $data['message'] ?? 'Unknown Twilio error';
            throw new RuntimeException("Twilio API error ({$httpCode}): {$msg}");
        }

        return $data ?? [];
    }
}