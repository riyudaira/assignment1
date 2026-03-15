<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'message' => 'required_without:image|nullable|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png',
        ];
    }
    public function messages()
    {
        return [
            'message.required_without' => '本文を入力してください',
            'message.max'              => '本文は400文字以内で入力してください',
            'image.mimes'              => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.image'              => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
