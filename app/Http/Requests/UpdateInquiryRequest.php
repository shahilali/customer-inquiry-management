<?php

namespace App\Http\Requests;

use App\Models\Inquiry;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'category' => ['sometimes', Rule::in(Inquiry::CATEGORIES)],
            'subject' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string', 'min:10'],
            'status' => ['sometimes', Rule::in(Inquiry::STATUSES)],
            'priority' => ['sometimes', Rule::in(Inquiry::PRIORITIES)],
            'resolution_notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.email' => 'Please provide a valid email address.',
            'category.in' => 'The selected category is invalid. Valid categories are: ' . implode(', ', Inquiry::CATEGORIES),
            'status.in' => 'The selected status is invalid. Valid statuses are: ' . implode(', ', Inquiry::STATUSES),
            'priority.in' => 'The selected priority is invalid. Valid priorities are: ' . implode(', ', Inquiry::PRIORITIES),
            'message.min' => 'The message must be at least 10 characters long.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'category' => 'inquiry category',
            'subject' => 'inquiry subject',
            'message' => 'inquiry message',
            'status' => 'inquiry status',
            'priority' => 'inquiry priority',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
