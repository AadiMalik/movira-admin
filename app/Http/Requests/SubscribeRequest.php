<?php

namespace App\Http\Requests;

use App\Models\CustomerCard;
use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subscription_package_id' => 'required|integer|exists:subscription_packages,id',
            'customer_card_id' => [
                'required',
                'integer',
                'exists:customer_cards,id',
                function ($attribute, $value, $fail) {
                    $card = CustomerCard::find($value);
                    if ($card && $card->user_id !== auth()->id()) {
                        $fail('The selected card does not belong to you.');
                    }
                }
            ],
        ];
    }
}
