<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends BaseTenantModel
{
    use HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'slug',
        'parent_id',
        'description',
        'image',
        'status',
        'sort_order',
        'is_featured',
        'show_in_menu',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'show_in_menu' => 'boolean',
        ];
    }

    /**
     * Boot the model and register events for code and slug.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Category $model): void {
            if (empty($model->code)) {
                $model->code = static::nextCodeForCompany($model->company_id);
            }
        });

        static::saving(function (Category $model): void {
            if (! empty($model->name) && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Generate next CAT-XXXX code per company.
     */
    public static function nextCodeForCompany(?string $companyId): string
    {
        if (! $companyId) {
            return 'CAT-0001';
        }

        $max = static::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNotNull('code')
            ->where('code', 'like', 'CAT-%')
            ->pluck('code')
            ->map(fn (string $code) => (int) preg_replace('/^CAT-/', '', $code))
            ->max();

        return 'CAT-' . str_pad((string) (($max ?? 0) + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the path for the thumbnail derived from the main image.
     */
    public function getThumbnailPathAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_ends_with($this->image, '.webp')) {
            return substr($this->image, 0, -5) . '_thumb.webp';
        }

        return $this->image;
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the company that owns the category.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
