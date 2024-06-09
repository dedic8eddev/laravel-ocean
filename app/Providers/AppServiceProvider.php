<?php

namespace App\Providers;

use App\Services\JwtService\JwtService;
use App\Services\JwtService\JwtServiceImpl;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->extendValidator();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function extendValidator() {
        /**
         * Works like `exists` validation rule, for an array of objects
         * where we validate based on a specific property.
         *
         * example:
         * Input: { objects: [{id: 1, ...}, {id: 2, ...}, ..., {id: 13, ...}] }
         * exists Rule: "objects.*.id" => "exists:table,table_id" N queries
         * this Rule: "exists_property:id,table,table_id" 1 query
         */
        Validator::extend('exists_property', function($attribute, $value, $parameters, $validator) {
            list($property, $table, $column) = $parameters;

            $propertyValues = array_map(function($valueEntry) use ($property) {
                return $valueEntry[$property];
            }, $value);

            return $validator->validateExists($attribute, $propertyValues, [$table, $column]);
        });
    }
}
