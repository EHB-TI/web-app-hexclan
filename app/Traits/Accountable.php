<?php

namespace App\Traits;

trait Accountable
{
    public static function bootAccountable()
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = request()->user()->user_id ?? request()->user()->id; // Order is important.
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = request()->user()->user_id ?? request()->user()->id;
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = request()->user()->user_id ?? request()->user()->id;
            }
        });
    }
}
