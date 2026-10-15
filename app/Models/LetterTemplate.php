<?php

namespace App\Models;

use App\Enums\Config as ConfigEnum;
use App\Enums\TemplateType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'reference_number',
        'regarding',
        'attachment',
        'letter_date',
        'letter_place',
        'to_name',
        'to_address',
        'opening_salutation',
        'opening_text',
        'content',
        'closing_text',
        'closing_salutation',
        'signer_position',
        'signer_name',
        'signer_identity_number',
        'copy_to',
        'specific_data',
        'user_id',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'specific_data' => 'array',
    ];

    protected $appends = [
        'formatted_letter_date',
        'type_label',
        'type_badge',
    ];

    public function getFormattedLetterDateAttribute(): string
    {
        return $this->letter_date ? Carbon::parse($this->letter_date)->isoFormat('D MMMM YYYY') : '-';
    }

    public function getTypeEnumAttribute(): ?TemplateType
    {
        return TemplateType::tryFrom($this->type);
    }

    public function getTypeLabelAttribute(): string
    {
        $enum = $this->type_enum;
        return $enum ? $enum->label() : ucfirst($this->type);
    }

    public function getTypeBadgeAttribute(): string
    {
        $enum = $this->type_enum;
        return $enum ? $enum->badgeColor() : 'secondary';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where(function ($q) use ($find) {
                $q->where('title', 'LIKE', "%$find%")
                    ->orWhere('reference_number', 'LIKE', "%$find%")
                    ->orWhere('regarding', 'LIKE', "%$find%")
                    ->orWhere('to_name', 'LIKE', "%$find%");
            });
        });
    }

    public function scopeType($query, $type)
    {
        return $query->when($type, function ($query, $selectedType) {
            return $query->where('type', $selectedType);
        });
    }

    public function scopeRender($query, $search = null, $type = null)
    {
        $pageSize = 10;
        try {
            $pageSize = (int) Config::getValueByCode(ConfigEnum::PAGE_SIZE);
        } catch (\Throwable $e) {
            $pageSize = 10;
        }

        return $query
            ->with('user')
            ->search($search)
            ->type($type)
            ->latest()
            ->paginate($pageSize)
            ->appends([
                'search' => $search,
                'type' => $type,
            ]);
    }
}
