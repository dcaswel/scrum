<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateGuidelineRequest
 *
 * @property string $description
 * @property array $bullets
 * @property array $tickets
 */
class CreateGuidelineRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'score' => 'required|numeric',
            'description' => 'required|string',
            'bullets' => 'present|array',
            'bullets.*.body' => 'required|string',
            'bullets.*.id' => 'integer|exists:guideline_bullets',
            'tickets' => 'present|array',
            'tickets.*.ticket_number' => 'required|string',
            'tickets.*.id' => 'integer|exists:guideline_tickets',
        ];
    }

    public function messages(): array
    {
        return [
            'bullets.*.body.required' => 'The bullet body is required.',
            'tickets.*.ticket_number.required' => 'The ticket\'s number is required',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('manageGuidelines', $this->user()->currentTeam);
    }
}
