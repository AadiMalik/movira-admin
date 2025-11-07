<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Models\Country;
use App\Http\Controllers\ApiController;
use App\Transformers\CountryTransformer;

/**
 * @group Countries
 *
 * Get countries
 */
class CountryController extends ApiController
{
    /**
     * Get all the countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // $countriesQuery = Country::active();

        $countries = Country::active()->orderBy('name')->get();

        if ($countries->isEmpty()) {
            return $this->respondNotFound('No countries found');
        }

        return $this->respondOk($countries);
    }
}
