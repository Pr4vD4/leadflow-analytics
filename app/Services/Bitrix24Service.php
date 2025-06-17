<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class Bitrix24Service
{

    /**
     * Отправляет лид в Битрикс24 через вебхук
     *
     * @param Lead $lead
     * @return bool
     */
    public function sendLead(Lead $lead): bool
    {
        try {
            $company = $lead->company;

            // Проверяем, включена ли интеграция с Битрикс24
            if (!$this->isBitrix24Enabled($company)) {
                Log::info('Битрикс24 интеграция отключена для компании: ' . $company->id);
                return false;
            }

            $webhookUrl = $this->getBitrix24WebhookUrl($company);

            if (!$webhookUrl) {
                Log::warning('Не найден вебхук URL для Битрикс24 для компании: ' . $company->id);
                return false;
            }

            // Формируем данные для отправки в Битрикс24
            $leadData = $this->prepareLead($lead);

            // Отправляем запрос в Битрикс24
            $response = Http::timeout(30)->post($webhookUrl . 'crm.lead.add.json', [
                'fields' => $leadData
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['result'])) {
                    Log::info('Лид успешно отправлен в Битрикс24', [
                        'lead_id' => $lead->id,
                        'bitrix_lead_id' => $responseData['result']
                    ]);

                    // Сохраняем ID лида в Битрикс24 для дальнейшего использования
                    $this->saveBitrix24LeadId($lead, $responseData['result']);

                    return true;
                } else {
                    Log::error('Ошибка в ответе Битрикс24', [
                        'lead_id' => $lead->id,
                        'response' => $responseData
                    ]);
                    return false;
                }
            } else {
                Log::error('Ошибка HTTP запроса в Битрикс24', [
                    'lead_id' => $lead->id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (Exception $e) {
            Log::error('Исключение при отправке лида в Битрикс24: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Проверяет, включена ли интеграция с Битрикс24 для компании
     *
     * @param Company $company
     * @return bool
     */
    private function isBitrix24Enabled(Company $company): bool
    {
        $settings = $company->settings ?? [];
        return isset($settings['integrations']['bitrix24']['enabled']) &&
               $settings['integrations']['bitrix24']['enabled'] === true;
    }

    /**
     * Получает URL вебхука Битрикс24 для компании
     *
     * @param Company $company
     * @return string|null
     */
    private function getBitrix24WebhookUrl(Company $company): ?string
    {
        $settings = $company->settings ?? [];
        return $settings['integrations']['bitrix24']['webhook_url'] ?? null;
    }

    /**
     * Подготавливает данные лида для отправки в Битрикс24
     *
     * @param Lead $lead
     * @return array
     */
    private function prepareLead(Lead $lead): array
    {
        $leadData = [
            'TITLE' => $lead->name ?: 'Новый лид',
            'SOURCE_ID' => 'WEB', // Источник лида
            'SOURCE_DESCRIPTION' => $lead->source,
            'STATUS_ID' => 'NEW', // Статус лида
            'OPENED' => 'Y', // Доступен для всех
        ];

        // Добавляем контактную информацию
        if ($lead->name) {
            $leadData['NAME'] = $lead->name;
        }

        if ($lead->email) {
            $leadData['EMAIL'] = [
                [
                    'VALUE' => $lead->email,
                    'VALUE_TYPE' => 'WORK'
                ]
            ];
        }

        if ($lead->phone) {
            $leadData['PHONE'] = [
                [
                    'VALUE' => $lead->phone,
                    'VALUE_TYPE' => 'WORK'
                ]
            ];
        }

        if ($lead->message) {
            $leadData['COMMENTS'] = $lead->message;
        }

        // Добавляем кастомные поля, если есть
        if ($lead->custom_fields && is_array($lead->custom_fields)) {
            foreach ($lead->custom_fields as $key => $value) {
                $leadData['UF_CRM_' . strtoupper($key)] = $value;
            }
        }

        return $leadData;
    }

    /**
     * Сохраняет ID лида в Битрикс24 в кастомных полях лида
     *
     * @param Lead $lead
     * @param int $bitrixLeadId
     * @return void
     */
    private function saveBitrix24LeadId(Lead $lead, int $bitrixLeadId): void
    {
        try {
            $customFields = $lead->custom_fields ?? [];
            $customFields['bitrix24_lead_id'] = $bitrixLeadId;

            $lead->update(['custom_fields' => $customFields]);
        } catch (Exception $e) {
            Log::error('Ошибка при сохранении ID лида Битрикс24: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'bitrix_lead_id' => $bitrixLeadId
            ]);
        }
    }
}
