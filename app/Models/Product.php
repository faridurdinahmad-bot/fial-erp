<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Product extends BaseTenantModel
{
    use HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'category_id',
        'product_group_id',
        'brand_id',
        'unit_id',
        'warranty_id',
        'name',
        'sku',
        'description',
        'image',
        'status',
        'retail_price',
        'wholesale_price',
        'super_wholesale_price',
        'track_stock',
        'opening_stock',
        'low_stock_alert',
        'video_url',
        'slug',
        'meta_title',
        'meta_description',
        'external_sync_id',
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
            'retail_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'super_wholesale_price' => 'decimal:2',
            'track_stock' => 'boolean',
        ];
    }

    /**
     * Get the category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the product group this product belongs to.
     */
    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id');
    }

    /**
     * Get the brand this product belongs to.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the unit this product uses.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the warranty for this product.
     */
    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    /**
     * Get the company that owns the product.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the package definitions for this product.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(ProductPackage::class);
    }

    /**
     * Sync package definitions from a request-like array.
     *
     * @param array<int, array<string, mixed>> $packages
     */
    public function syncPackages(array $packages): void
    {
        // For now, keep it simple: remove existing packages and recreate from form input.
        $this->packages()->delete();

        foreach ($packages as $package) {
            $name = trim((string) Arr::get($package, 'name', ''));
            $quantity = Arr::get($package, 'quantity');

            if ($name === '' || $quantity === null || $quantity === '') {
                continue;
            }

            $status = (bool) Arr::get($package, 'status', true);

            $this->packages()->create([
                'name' => $name,
                'quantity' => $quantity,
                'status' => $status,
            ]);
        }
    }
}
