<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTenantModel extends Model
{
    /**
     * Boot the model and register tenant scope and company_id auto-fill.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function (Model $model): void {
            if (empty($model->company_id) && app()->bound('currentCompany')) {
                $model->company_id = app('currentCompany')->id;
            }
        });
    }
}
