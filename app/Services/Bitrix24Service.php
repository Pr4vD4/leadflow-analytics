<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class Bitrix24Service
{
    private string $webhookUrl;
    private array $defaultHeaders;

    public function __construct(string $webhookUrl = null)
    {
        $this->webhookUrl = $webhookUrl ?: config('services.bitrix24.webhook_url');
        $this->defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create a lead in Bitrix24
     *
     * @param array $leadData
     * @return array
     * @throws Exception
     */
    public function createLead(array $leadData): array
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->timeout(30)
                ->post($this->webhookUrl . 'crm.lead.add.json', [
                    'fields' => $this->prepareBitrixLeadData($leadData)
                ]);

            if (!$response->successful()) {
                throw new Exception('Bitrix24 API error: ' . $response->body());
            }

            $result = $response->json();

            if (isset($result['error'])) {
                throw new Exception('Bitrix24 error: ' . $result['error_description']);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Bitrix24 create lead error: ' . $e->getMessage(), [
                'lead_data' => $leadData
            ]);
            throw $e;
        }
    }

    /**
     * Update a lead in Bitrix24
     *
     * @param int $bitrixLeadId
     * @param array $leadData
     * @return array
     * @throws Exception
     */
    public function updateLead(int $bitrixLeadId, array $leadData): array
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->timeout(30)
                ->post($this->webhookUrl . 'crm.lead.update.json', [
                    'id' => $bitrixLeadId,
                    'fields' => $this->prepareBitrixLeadData($leadData)
                ]);

            if (!$response->successful()) {
                throw new Exception('Bitrix24 API error: ' . $response->body());
            }

            $result = $response->json();

            if (isset($result['error'])) {
                throw new Exception('Bitrix24 error: ' . $result['error_description']);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Bitrix24 update lead error: ' . $e->getMessage(), [
                'bitrix_lead_id' => $bitrixLeadId,
                'lead_data' => $leadData
            ]);
            throw $e;
        }
    }

    /**
     * Get lead from Bitrix24
     *
     * @param int $bitrixLeadId
     * @return array
     * @throws Exception
     */
    public function getLead(int $bitrixLeadId): array
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->timeout(30)
                ->post($this->webhookUrl . 'crm.lead.get.json', [
                    'id' => $bitrixLeadId
                ]);

            if (!$response->successful()) {
                throw new Exception('Bitrix24 API error: ' . $response->body());
            }

            $result = $response->json();

            if (isset($result['error'])) {
                throw new Exception('Bitrix24 error: ' . $result['error_description']);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Bitrix24 get lead error: ' . $e->getMessage(), [
                'bitrix_lead_id' => $bitrixLeadId
            ]);
            throw $e;
        }
    }

    /**
     * Prepare lead data for Bitrix24 format
     *
     * @param array $leadData
     * @return array
     */
    private function prepareBitrixLeadData(array $leadData): array
    {
        $bitrixData = [];

        // Основные поля
        if (isset($leadData['name'])) {
            $bitrixData['NAME'] = $leadData['name'];
        }

        if (isset($leadData['email'])) {
            $bitrixData['EMAIL'] = [
                ['VALUE' => $leadData['email'], 'VALUE_TYPE' => 'WORK']
            ];
        }

        if (isset($leadData['phone'])) {
            $bitrixData['PHONE'] = [
                ['VALUE' => $leadData['phone'], 'VALUE_TYPE' => 'WORK']
            ];
        }

        if (isset($leadData['message'])) {
            $bitrixData['COMMENTS'] = $leadData['message'];
        }

        if (isset($leadData['source'])) {
            $bitrixData['SOURCE_DESCRIPTION'] = $leadData['source'];
        }

        // Пользовательские поля
        if (isset($leadData['custom_fields']) && is_array($leadData['custom_fields'])) {
            foreach ($leadData['custom_fields'] as $key => $value) {
                // Bitrix24 пользовательские поля обычно начинаются с UF_CRM_
                $bitrixData['UF_CRM_' . strtoupper($key)] = $value;
            }
        }

        // Статус по умолчанию
        $bitrixData['STATUS_ID'] = 'NEW';

        return $bitrixData;
    }

    /**
     * Test Bitrix24 connection
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->timeout(10)
                ->post($this->webhookUrl . 'crm.lead.fields.json');

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Bitrix24 connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available lead fields from Bitrix24
     *
     * @return array
     * @throws Exception
     */
    public function getLeadFields(): array
    {
        try {
            $response = Http::withHeaders($this->defaultHeaders)
                ->timeout(30)
                ->post($this->webhookUrl . 'crm.lead.fields.json');

            if (!$response->successful()) {
                throw new Exception('Bitrix24 API error: ' . $response->body());
            }

            $result = $response->json();

            if (isset($result['error'])) {
                throw new Exception('Bitrix24 error: ' . $result['error_description']);
            }

            return $result['result'] ?? [];
        } catch (Exception $e) {
            Log::error('Bitrix24 get lead fields error: ' . $e->getMessage());
            throw $e;
        }
    }
}
