<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Sms extends BaseConfig
{
    /**
     * The API endpoint URL your SMS provider gave you.
     * Example (BulkSMSBD-style): 'https://bulksmsbd.net/api/smsapi'
     */
    public string $apiUrl = '';

    /**
     * Your API key / auth token from the provider's dashboard.
     */
    public string $apiKey = '';

    /**
     * Sender ID, if your provider requires one (often a masked name approved by them).
     */
    public string $senderId = '';

    /**
     * Set to false in production once real credentials are filled in above.
     * While true, messages are only written to the sms_logs table and not
     * actually sent - useful for testing without spending SMS credit.
     */
    public bool $dryRun = true;
}
