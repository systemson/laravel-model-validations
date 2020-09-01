<?php

namespace Systemson\ModelValidations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
trait ValidationsTrait
{
    public static function boot()
    {
        parent::boot();

        self::creating(function (Model $model) {
            $model->validate();
        });

        self::updating(function (Model $model) {
            $model->validate();
        });
    }

    public function validate()
    {
        if (empty($this->getValidations())) {
            return true;
        }

        Validator::make(
            $this->getAttributes(),
            $this->getValidations()
        )->validate();

        return true;
    }

    public function getValidations()
    {
        if (!$this->exists && method_exists($this, 'getCreateValidations')) {
            return $this->getCreateValidations();
        }


        if (method_exists($this, 'getUpdateValidations')) {
            return $this->getUpdateValidations();
        }

        return $this->validations = [];
    }
}
