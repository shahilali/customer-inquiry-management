<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class InquiryService
{
    /**
     * Get all inquiries with optional filtering and pagination.
     */
    public function getAllInquiries(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $query = Inquiry::query();

            if (isset($filters['category'])) {
                $query->byCategory($filters['category']);
            }

            if (isset($filters['status'])) {
                $query->byStatus($filters['status']);
            }

            if (isset($filters['priority'])) {
                $query->byPriority($filters['priority']);
            }

            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            }

            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching inquiries', [
                'filters' => $filters,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get a single inquiry by ID.
     */
    public function getInquiryById(int $id): ?Inquiry
    {
        try {
            return Inquiry::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching inquiry', [
                'inquiry_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a new inquiry with transaction handling.
     */
    public function createInquiry(array $data): Inquiry
    {
        try {
            return DB::transaction(function () use ($data) {
                $inquiry = Inquiry::create($data);

                Log::info('New inquiry created', [
                    'inquiry_id' => $inquiry->id,
                    'category' => $inquiry->category,
                    'email' => $inquiry->email,
                ]);

                return $inquiry;
            });
        } catch (\Exception $e) {
            Log::error('Error creating inquiry', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing inquiry with transaction handling.
     */
    public function updateInquiry(int $id, array $data): Inquiry
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $inquiry = Inquiry::findOrFail($id);

                $oldStatus = $inquiry->status;
                $inquiry->update($data);

                if (isset($data['status']) && $data['status'] === 'resolved' && $oldStatus !== 'resolved') {
                    $inquiry->resolved_at = now();
                    $inquiry->save();
                }

                Log::info('Inquiry updated', [
                    'inquiry_id' => $inquiry->id,
                    'updated_fields' => array_keys($data),
                    'old_status' => $oldStatus,
                    'new_status' => $inquiry->status,
                ]);

                return $inquiry->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Error updating inquiry', [
                'inquiry_id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete an inquiry (soft delete) with transaction handling.
     */
    public function deleteInquiry(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $inquiry = Inquiry::findOrFail($id);
                $deleted = $inquiry->delete();

                Log::info('Inquiry deleted', [
                    'inquiry_id' => $id,
                    'category' => $inquiry->category,
                ]);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Error deleting inquiry', [
                'inquiry_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get inquiry statistics.
     */
    public function getStatistics(): array
    {
        try {
            return [
                'total' => Inquiry::count(),
                'by_status' => [
                    'pending' => Inquiry::where('status', 'pending')->count(),
                    'in_progress' => Inquiry::where('status', 'in_progress')->count(),
                    'resolved' => Inquiry::where('status', 'resolved')->count(),
                    'closed' => Inquiry::where('status', 'closed')->count(),
                ],
                'by_category' => [
                    'Trading' => Inquiry::where('category', 'Trading')->count(),
                    'Market Data' => Inquiry::where('category', 'Market Data')->count(),
                    'Technical Issues' => Inquiry::where('category', 'Technical Issues')->count(),
                    'General Questions' => Inquiry::where('category', 'General Questions')->count(),
                ],
                'by_priority' => [
                    'low' => Inquiry::where('priority', 'low')->count(),
                    'medium' => Inquiry::where('priority', 'medium')->count(),
                    'high' => Inquiry::where('priority', 'high')->count(),
                    'urgent' => Inquiry::where('priority', 'urgent')->count(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching inquiry statistics', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
