<?php

namespace App\Http\Requests;

use App\Enums\TemplateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLetterTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'title' => __('model.template.title'),
            'type' => __('model.template.type'),
            'reference_number' => __('model.template.reference_number'),
            'regarding' => __('model.template.regarding'),
            'attachment' => __('model.template.attachment'),
            'letter_date' => __('model.template.letter_date'),
            'letter_place' => __('model.template.letter_place'),
            'to_name' => __('model.template.to_name'),
            'to_address' => __('model.template.to_address'),
            'opening_salutation' => __('model.template.opening_salutation'),
            'opening_text' => __('model.template.opening_text'),
            'content' => __('model.template.content'),
            'closing_text' => __('model.template.closing_text'),
            'closing_salutation' => __('model.template.closing_salutation'),
            'signer_position' => __('model.template.signer_position'),
            'signer_name' => __('model.template.signer_name'),
            'signer_identity_number' => __('model.template.signer_identity_number'),
            'copy_to' => __('model.template.copy_to'),
            'specific_data' => __('model.template.specific_data'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(TemplateType::class)],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'regarding' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'string', 'max:255'],
            'letter_date' => ['nullable', 'date'],
            'letter_place' => ['nullable', 'string', 'max:255'],
            'to_name' => ['nullable', 'string', 'max:255'],
            'to_address' => ['nullable', 'string'],
            'opening_salutation' => ['nullable', 'string', 'max:255'],
            'opening_text' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'closing_text' => ['nullable', 'string'],
            'closing_salutation' => ['nullable', 'string', 'max:255'],
            'signer_position' => ['nullable', 'string', 'max:255'],
            'signer_name' => ['nullable', 'string', 'max:255'],
            'signer_identity_number' => ['nullable', 'string', 'max:255'],
            'copy_to' => ['nullable', 'string'],
            'specific_data' => ['nullable', 'array'],
        ];
    }
}
