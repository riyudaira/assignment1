<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'name' => 'required|string|max:20',
            'post_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => 'required|string|max:255',
            'build' => 'nullable|string|max:255',
        ];
    }
    public function messages()
    {
        return [
            'profile_image.image' => '画像ファイルを選択してください。',
            'profile_image.mimes' => '画像はjpegまたはpng形式でアップロードしてください。',
            'profile_image.max' => '画像サイズは2MB以下にしてください。',
            'name.required' => 'ユーザー名を入力してください。',
            'name.max' => 'ユーザー名は20文字以内で入力してください。',
            'post_code.required' => '郵便番号を入力してください。',
            'post_code.regex' => '郵便番号はハイフンありの8文字の形式で入力してください。',
            'address.required' => '住所を入力してください。',
        ];
    }
    public function attributes()
    {
        return [
            'profile_image' => 'プロフィール画像',
            'name' => 'ユーザー名',
            'post_code' => '郵便番号',
            'address' => '住所',
            'build' => '建物名',
        ];
    }
    //半角へ自動修正
    protected function prepareForValidation()
    {
        $this->merge([
            'post_code' => mb_convert_kana($this->post_code, 'as'),
        ]);
    }
}
