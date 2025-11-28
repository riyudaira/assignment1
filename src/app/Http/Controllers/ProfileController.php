<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    //プロフィール表示
    public function profile(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab');

        if ($tab === 'purchased') {
            $items = $user->purchases()->with('item')->get()->pluck('item');
        } else {
            $items = $user->items;
        }

        return view('user.profile', compact('items'));
    }

    //更新画面表示
    public function edit()
    {
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    //更新の処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->name = $request->input('name');
        $user->post_code = $request->input('post_code');
        $user->address = $request->input('address');
        $user->build = $request->input('build');

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }
        $user->profile_completed = true;
        $user->save();

        return redirect()->route('items.index')->with('message', 'プロフィールを更新しました');
    }
}
