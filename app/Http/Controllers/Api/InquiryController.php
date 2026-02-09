<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use App\Http\Resources\InquiryCollection;
use App\Http\Resources\InquiryResource;
use App\Services\InquiryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    public function __construct(
        protected InquiryService $inquiryService
    ) {}

    /**
     * Display a listing of inquiries.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'category' => $request->query('category'),
                'status' => $request->query('status'),
                'priority' => $request->query('priority'),
                'search' => $request->query('search'),
                'sort_by' => $request->query('sort_by', 'created_at'),
                'sort_order' => $request->query('sort_order', 'desc'),
            ];

            $perPage = (int) $request->query('per_page', 15);
            $perPage = min(max($perPage, 1), 100);

            $inquiries = $this->inquiryService->getAllInquiries($filters, $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Inquiries retrieved successfully',
                'data' => new InquiryCollection($inquiries),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in InquiryController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiries',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request',
            ], 500);
        }
    }

    /**
     * Store a newly created inquiry.
     */
    public function store(StoreInquiryRequest $request): JsonResponse
    {
        try {
            $inquiry = $this->inquiryService->createInquiry($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Inquiry submitted successfully',
                'data' => new InquiryResource($inquiry),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in InquiryController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create inquiry',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request',
            ], 500);
        }
    }

    /**
     * Display the specified inquiry.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $inquiry = $this->inquiryService->getInquiryById($id);

            return response()->json([
                'success' => true,
                'message' => 'Inquiry retrieved successfully',
                'data' => new InquiryResource($inquiry),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found',
                'error' => "No inquiry found with ID: {$id}",
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in InquiryController@show', [
                'inquiry_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiry',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request',
            ], 500);
        }
    }

    /**
     * Update the specified inquiry.
     */
    public function update(UpdateInquiryRequest $request, int $id): JsonResponse
    {
        try {
            $inquiry = $this->inquiryService->updateInquiry($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Inquiry updated successfully',
                'data' => new InquiryResource($inquiry),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found',
                'error' => "No inquiry found with ID: {$id}",
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in InquiryController@update', [
                'inquiry_id' => $id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update inquiry',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request',
            ], 500);
        }
    }

    /**
     * Remove the specified inquiry.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->inquiryService->deleteInquiry($id);

            return response()->json([
                'success' => true,
                'message' => 'Inquiry deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found',
                'error' => "No inquiry found with ID: {$id}",
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in InquiryController@destroy', [
                'inquiry_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete inquiry',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request',
            ], 500);
        }
    }

    /**
     * Get inquiry statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->inquiryService->getStatistics();

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in InquiryController@statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while processing your request',
            ], 500);
        }
    }
}
