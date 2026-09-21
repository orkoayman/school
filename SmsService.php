<?php

namespace App\Libraries;

use App\Models\SmsLogModel;
use Config\Sms as SmsConfig;

/**
 * Generic SMS sender. Works with most Bangladeshi SMS gateway APIs
 * (SSL Wireless, Alpha SMS, BulkSMSBD, etc.) which typically accept
 * a simple GET/POST request with api_key, sender_id, number, and message.
 *
 * IMPORTANT: The request format below is written for a generic
 * "BulkSMSBD-style" API as a working example. Your specific provider's
 * parameter names may differ slightly (check their API documentation) -
 * adjust the $params array in send() to match exactly what your provider expects.
 */
class SmsService
{
    protected SmsConfig $config;
    protected SmsLogModel $logModel;

    public function __construct()
    {
        $this->config   = config(SmsConfig::class);
        $this->logModel = model(SmsLogModel::class);
    }

    /**
     * Send an SMS and log the attempt. Returns true on (assumed) success.
     */
    public function send(string $phone, string $message, string $type = 'other', ?int $studentId = null): bool
    {
        $phone = $this->normalizePhone($phone);

        if ($this->config->dryRun || empty($this->config->apiUrl)) {
            // No real gateway configured yet - just log it so you can verify the flow works.
            $this->logModel->insert([
                'student_id' => $studentId,
                'phone'      => $phone,
                'message'    => $message,
                'type'       => $type,
                'status'     => 'sent',
                'response'   => 'DRY RUN - no gateway configured, message not actually sent',
            ]);

            return true;
        }

        $params = [
            'api_key'   => $this->config->apiKey,
            'senderid'  => $this->config->senderId,
            'number'    => $phone,
            'message'   => $message,
        ];

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($this->config->apiUrl, [
                'query'   => $params,
                'timeout' => 10,
            ]);

            $body    = $response->getBody();
            $success = $response->getStatusCode() === 200;

            $this->logModel->insert([
                'student_id' => $studentId,
                'phone'      => $phone,
                'message'    => $message,
                'type'       => $type,
                'status'     => $success ? 'sent' : 'failed',
                'response'   => $body,
            ]);

            return $success;
        } catch (\Throwable $e) {
            $this->logModel->insert([
                'student_id' => $studentId,
                'phone'      => $phone,
                'message'    => $message,
                'type'       => $type,
                'status'     => 'failed',
                'response'   => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Normalizes Bangladeshi phone numbers to the 880XXXXXXXXXX format
     * that most local gateways expect (no leading +, no leading 0).
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // strip non-digits

        if (str_starts_with($phone, '880')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '880' . substr($phone, 1);
        }

        return '880' . $phone;
    }

    public function attendanceMessage(string $studentName, string $status, string $time): string
    {
        $statusText = $status === 'present' ? 'উপস্থিত হয়েছে' : 'অনুপস্থিত';

        return "প্রিয় অভিভাবক, আপনার সন্তান {$studentName} আজ {$time} সময়ে স্কুলে {$statusText}।";
    }
}
