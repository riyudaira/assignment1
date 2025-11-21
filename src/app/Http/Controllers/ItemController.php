<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Like;
use App\Http\Requests\SellRequest;

class ItemController extends Controller
{
    //商品一覧を表示(検索結果表示も)
    public function index(Request $request)
    {
        $tab = $request->input('tab');
        $keyword = $request->input('keyword');

        if ($tab === 'mylist') {
            if (Auth::check()) {
                $user = Auth::user();

                $likedItems = $user->likes()
                    ->with('item')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($like) {
                        return $like->item;
                    });

                if ($keyword) {
                    $searchResults = Item::where('name', 'like', "%{$keyword}%")->get();

                    $items = $likedItems->merge($searchResults)->unique('id');
                } else {
                    $items = $likedItems;
                }
            } else {
                $items = collect();
                session()->flash('message', 'マイリストを表示するにはログインしてください。');
            }
        } else {
            $items = Item::query();

            if ($keyword) {
                $items->where('name', 'like', "%{$keyword}%");
            }

            $items = $items->with('purchase')->get();
        }

        return view('items.index', compact('items'));
    }

    //商品詳細を表示
    public function detail(Item $item)
    {
        $item->load(['likes', 'comments.user', 'categories', 'purchase']);
        return view('items.detail', compact('item'));
    }

    //いいね押下でカウント
    public function like(Item $item)
    {
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            Like::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
        }
        return response()->json(['likes_count' => $item->likes()->count()]);
    }

    //出品画面を表示
    public function sell()
    {
        $categories = Category::all();
        return view('items.sell', compact('categories'));
    }

    //出品情報登録
    public function store(SellRequest $request)
    {
        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->input('name');
        $item->brand = $request->input('brand');
        $item->description = $request->input('description');
        $item->price = $request->input('price');
        $item->condition = $request->input('condition');
        $item->category_id = $request->input('category_id');

        if ($request->hasFile('item_image')) {
            $path = $request->file('item_image')->store('item_images', 'public');
            $item->image_path = '/storage/' . $path;
        }

        $item->save();

        if ($request->filled('categories')) {
            $item->categories()->sync($request->input('categories'));
        }

        return redirect()->route('user.profile')->with('message', '商品を出品しました');
    }
}
