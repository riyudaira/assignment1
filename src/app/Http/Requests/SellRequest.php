<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'condition' => 'required|string',
            'categories' => 'required|array|min:1',
            'item_image' => 'required|image|mimes:jpeg,png|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'price.required' => '価格を入力してください。',
            'price.integer' => '価格は数値で入力してください。',
            'price.min' => '価格は0円以上で入力してください。',
            'condition.required' => '商品の状態を選択してください。',
            'categories.required' => 'カテゴリーを1つ以上選択してください。',
            'item_image.required' => '商品画像をアップロードしてください。',
            'item_image.image' => '画像ファイルを選択してください。',
            'item_image.mimes' => '画像はjpegまたはpng形式でアップロードしてください。',
            'item_image.max' => '画像サイズは2MB以下にしてください。',
        ];
    }
    public function attributes()
    {
        return [
            'name' => '商品名',
            'brand' => 'ブランド名',
            'description' => '商品説明',
            'price' => '価格',
            'condition' => '商品の状態',
            'categories' => 'カテゴリー',
            'item_image' => '商品画像',
        ];
    }
}
