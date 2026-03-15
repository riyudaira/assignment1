<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;


class ProfileController extends Controller
{
    //プロフィール表示
    public function profile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tab = $request->input('tab');
        $dealingQuery = Item::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        })
            ->where(function ($query) use ($user) {
                $query->where('status', 'shipping')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('status', 'sold')
                            ->whereDoesntHave('reviews', function ($r) use ($user) {
                                $r->where('reviewer_id', $user->id);
                            });
                    });
            })
            ->withCount(['chats as unread_chats_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->where('is_read', false);
            }])
            ->addSelect([
                'latest_chat_at' => \App\Models\Chat::select('created_at')
                    ->whereColumn('item_id', 'items.id')
                    ->latest()
                    ->take(1)
            ])
            ->orderByRaw('COALESCE(latest_chat_at, items.created_at) DESC');
        $dealingItems = $dealingQuery->get();
        $dealingCount = $dealingItems->count();
        if ($tab === 'purchased') {
            $items = $user->purchases()->with('item')->get()->pluck('item');
        } elseif ($tab === 'dealing') {
            $items = $dealingItems;
        } else {
            $items = $user->items;
        }
        return view('user.profile', compact('items', 'dealingCount'));
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
        /** @var \App\Models\User $user */
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
