<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Filters by company_id when current company is set in the container.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! app()->bound('currentCompany')) {
            return;
        }

        $company = app('currentCompany');
        $builder->where($model->getTable() . '.company_id', $company->id);
    }
}
