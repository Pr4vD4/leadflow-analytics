<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Store a newly created lead in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'source' => 'required|string|max:255',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|required_without:phone',
                'phone' => 'nullable|string|max:20|required_without:email',
                'message' => 'nullable|string',
                'custom_fields' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'status' => 'error'
                ], 400);
            }

            // Get company from request attributes (set by middleware)
            $company = $request->attributes->get('company');

            // Create lead
            $lead = new Lead();
            $lead->company_id = $company->id;
            $lead->source = $request->input('source');
            $lead->name = $request->input('name');
            $lead->email = $request->input('email');
            $lead->phone = $request->input('phone');
            $lead->message = $request->input('message');
            $lead->custom_fields = $request->input('custom_fields');
            $lead->save();

            // TODO: Add to queue for AI processing

            return response()->json([
                'message' => 'Lead created successfully',
                'lead_id' => $lead->id,
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating lead: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error creating lead',
                'status' => 'error'
            ], 500);
        }
    }
}
