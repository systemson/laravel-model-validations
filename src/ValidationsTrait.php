<?php

namespace Systemson\ModelValidations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * @todo Split create and update validations.
 */
trait ValidationsTrait
{
    public static function boot()
    {
        parent::boot();

        self::creating(function(Model $model){
            $model->validate();
        });

        self::updating(function(Model $model){
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
        if (empty($this->validations) || !$this->exists) {
            return $this->validations;
        }

        return array_map(function ($rule) {
            if (\Str::contains($rule, ['unique'])) {
                preg_match("/(?<=unique:)(.*?)(?=\|)/", $rule, $match);
                $search = end($match);

                $id = $this->{$this->primaryKey};

                $replace = "{$search},{$id},{$this->primaryKey}";

                $rule = str_replace($search, $replace, $rule);
            }

            return $rule;
        }, $this->validations);
    }
}