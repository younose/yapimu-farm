<?php

namespace App\Traits;

trait CreatedBy
{
    public static function bootCreatedBy()
    {
        static::creating(function ($model) {
            $column = $model->createdByColumn ?? 'created_by';
            if (! $model->isDirty($column)) {
                $model->{$column} = auth()->user()->id;
            }
        });
    }
}
