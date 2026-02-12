<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends BaseTenantModel
{
    use HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'brand_code',
        'name',
        'description',
        'logo',
        'status',
    ];

    /**
     * Boot and register creating event for auto brand_code.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Brand $model): void {
            if (! empty($model->brand_code)) {
                return;
            }
            $model->brand_code = self::nextBrandCodeForCompany($model->company_id);
        });
    }

    /**
     * Generate next BR-XXXX code for the given company (per-company increment).
     */
    public static function nextBrandCodeForCompany(?string $companyId): string
    {
        if (! $companyId) {
            return 'BR-0001';
        }
        $max = static::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNotNull('brand_code')
            ->where('brand_code', 'like', 'BR-%')
            ->pluck('brand_code')
            ->map(fn (string $code) => (int) preg_replace('/^BR-/', '', $code))
            ->max();

        return 'BR-' . str_pad((string) (($max ?? 0) + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /**
     * Get the company that owns the brand.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
