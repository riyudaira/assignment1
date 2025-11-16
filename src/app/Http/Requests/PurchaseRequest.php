<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method' => 'required|in:コンビニ払い,カード払い',
            'delivery_address' => 'required|string|max:255',
        ];
    }
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '支払い方法は「コンビニ払い」または「カード払い」から選択してください。',
            'delivery_address.required' => '配送先を入力してください。',
            'delivery_address.string' => '配送先は文字列で入力してください。',
            'delivery_address.max' => '配送先は255文字以内で入力してください。',
        ];
    }
}
