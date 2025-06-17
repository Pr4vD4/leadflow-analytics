<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Company;
use App\Services\Bitrix24Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BitrixWebhookController extends Controller
{
    private Bitrix24Service $bitrix24Service;

    public function __construct(Bitrix24Service $bitrix24Service)
    {
        $this->bitrix24Service = $bitrix24Service;
    }

    /**
     * Handle incoming webhook from Bitrix24
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Логируем входящий запрос
            Log::info('Bitrix24 webhook received', [
                'headers' => $request->headers->all(),
                'data' => $request->all()
            ]);

            // Валидация базовых данных
            $validator = Validator::make($request->all(), [
                'event' => 'required|string',
                'data' => 'required|array',
                'ts' => 'nullable|string',
                'auth' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid webhook data',
                    'errors' => $validator->errors(),
                    'status' => 'error'
                ], 400);
            }

            $event = $request->input('event');
            $data = $request->input('data');

            // Обработка разных типов событий
            return match($event) {
                'ONCRMLEADADD' => $this->handleLeadAdd($data),
                'ONCRMLEADUPDATE' => $this->handleLeadUpdate($data),
                'ONCRMLEADDELETE' => $this->handleLeadDelete($data),
                default => $this->handleUnknownEvent($event, $data)
            };

        } catch (\Exception $e) {
            Log::error('Bitrix24 webhook processing error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Webhook processing failed',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Handle lead creation event
     *
     * @param array $data
     * @return JsonResponse
     */
    private function handleLeadAdd(array $data): JsonResponse
    {
        try {
            // Получаем ID лида из Bitrix24
            $bitrixLeadId = $data['FIELDS']['ID'] ?? null;

            if (!$bitrixLeadId) {
                throw new \Exception('Missing lead ID in webhook data');
            }

            // Получаем полные данные лида из Bitrix24
            $bitrixLead = $this->bitrix24Service->getLead($bitrixLeadId);

            if (!isset($bitrixLead['result'])) {
                throw new \Exception('Failed to fetch lead data from Bitrix24');
            }

            $leadFields = $bitrixLead['result'];

            // Определяем компанию (можно настроить логику определения)
            $company = $this->determineCompany($leadFields);

            // Преобразуем данные Bitrix24 в формат нашей системы
            $leadData = $this->convertBitrixLeadToLocal($leadFields);
            $leadData['company_id'] = $company->id;

            // Создаем лид в нашей системе
            $lead = Lead::create($leadData);

            // Сохраняем связь с Bitrix24
            $lead->update([
                'external_id' => $bitrixLeadId,
                'external_source' => 'bitrix24'
            ]);

            Log::info('Lead created from Bitrix24 webhook', [
                'lead_id' => $lead->id,
                'bitrix_lead_id' => $bitrixLeadId
            ]);

            return response()->json([
                'message' => 'Lead created successfully',
                'lead_id' => $lead->id,
                'status' => 'success'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error handling Bitrix24 lead add: ' . $e->getMessage(), [
                'data' => $data
            ]);

            return response()->json([
                'message' => 'Failed to process lead creation',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Handle lead update event
     *
     * @param array $data
     * @return JsonResponse
     */
    private function handleLeadUpdate(array $data): JsonResponse
    {
        try {
            $bitrixLeadId = $data['FIELDS']['ID'] ?? null;

            if (!$bitrixLeadId) {
                throw new \Exception('Missing lead ID in webhook data');
            }

            // Находим лид в нашей системе
            $lead = Lead::where('external_id', $bitrixLeadId)
                       ->where('external_source', 'bitrix24')
                       ->first();

            if (!$lead) {
                // Если лид не найден, создаем новый
                return $this->handleLeadAdd($data);
            }

            // Получаем обновленные данные из Bitrix24
            $bitrixLead = $this->bitrix24Service->getLead($bitrixLeadId);

            if (!isset($bitrixLead['result'])) {
                throw new \Exception('Failed to fetch updated lead data from Bitrix24');
            }

            $leadFields = $bitrixLead['result'];
            $leadData = $this->convertBitrixLeadToLocal($leadFields);

            // Обновляем лид
            $lead->update($leadData);

            Log::info('Lead updated from Bitrix24 webhook', [
                'lead_id' => $lead->id,
                'bitrix_lead_id' => $bitrixLeadId
            ]);

            return response()->json([
                'message' => 'Lead updated successfully',
                'lead_id' => $lead->id,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling Bitrix24 lead update: ' . $e->getMessage(), [
                'data' => $data
            ]);

            return response()->json([
                'message' => 'Failed to process lead update',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Handle lead deletion event
     *
     * @param array $data
     * @return JsonResponse
     */
    private function handleLeadDelete(array $data): JsonResponse
    {
        try {
            $bitrixLeadId = $data['FIELDS']['ID'] ?? null;

            if (!$bitrixLeadId) {
                throw new \Exception('Missing lead ID in webhook data');
            }

            // Находим и помечаем лид как удаленный
            $lead = Lead::where('external_id', $bitrixLeadId)
                       ->where('external_source', 'bitrix24')
                       ->first();

            if ($lead) {
                // Вместо физического удаления помечаем статус
                $lead->update(['status' => 'archived']);

                Log::info('Lead archived from Bitrix24 webhook', [
                    'lead_id' => $lead->id,
                    'bitrix_lead_id' => $bitrixLeadId
                ]);
            }

            return response()->json([
                'message' => 'Lead deletion processed',
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling Bitrix24 lead delete: ' . $e->getMessage(), [
                'data' => $data
            ]);

            return response()->json([
                'message' => 'Failed to process lead deletion',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Handle unknown event types
     *
     * @param string $event
     * @param array $data
     * @return JsonResponse
     */
    private function handleUnknownEvent(string $event, array $data): JsonResponse
    {
        Log::warning('Unknown Bitrix24 webhook event', [
            'event' => $event,
            'data' => $data
        ]);

        return response()->json([
            'message' => 'Event received but not processed',
            'event' => $event,
            'status' => 'ignored'
        ]);
    }

    /**
     * Convert Bitrix24 lead data to local format
     *
     * @param array $bitrixFields
     * @return array
     */
    private function convertBitrixLeadToLocal(array $bitrixFields): array
    {
        $data = [
            'source' => 'bitrix24',
            'name' => $bitrixFields['NAME'] ?? null,
            'message' => $bitrixFields['COMMENTS'] ?? null,
            'status' => $this->convertBitrixStatus($bitrixFields['STATUS_ID'] ?? 'NEW'),
        ];

        // Извлекаем email
        if (isset($bitrixFields['EMAIL']) && is_array($bitrixFields['EMAIL'])) {
            $data['email'] = $bitrixFields['EMAIL'][0]['VALUE'] ?? null;
        }

        // Извлекаем телефон
        if (isset($bitrixFields['PHONE']) && is_array($bitrixFields['PHONE'])) {
            $data['phone'] = $bitrixFields['PHONE'][0]['VALUE'] ?? null;
        }

        // Собираем пользовательские поля
        $customFields = [];
        foreach ($bitrixFields as $key => $value) {
            if (str_starts_with($key, 'UF_CRM_')) {
                $customFields[str_replace('UF_CRM_', '', strtolower($key))] = $value;
            }
        }

        if (!empty($customFields)) {
            $data['custom_fields'] = $customFields;
        }

        return array_filter($data, fn($value) => $value !== null);
    }

    /**
     * Convert Bitrix24 status to local status
     *
     * @param string $bitrixStatus
     * @return string
     */
    private function convertBitrixStatus(string $bitrixStatus): string
    {
        return match($bitrixStatus) {
            'NEW' => 'new',
            'IN_PROCESS' => 'in_progress',
            'PROCESSED' => 'completed',
            'JUNK' => 'archived',
            default => 'new'
        };
    }

    /**
     * Determine company for the lead
     *
     * @param array $leadFields
     * @return Company
     */
    private function determineCompany(array $leadFields): Company
    {
        // По умолчанию используем первую компанию
        // Можно расширить логику на основе пользовательских полей или других данных
        return Company::first() ?? Company::create([
            'name' => 'Default Company',
            'slug' => 'default'
        ]);
    }
}
