<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Chat;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompleted;

class ChatController extends Controller
{
    // チャット表示
    public function show(Item $item)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->id !== $item->user_id && $user->id !== $item->buyer_id) {
            abort(403);
        }
        Chat::where('item_id', $item->id)
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        $messages = Chat::where('item_id', $item->id)->orderBy('created_at', 'asc')->get();
        $otherItems = Item::where('status', 'shipping')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('buyer_id', $user->id);
            })
            ->where('id', '!=', $item->id)
            ->get();
        $alreadyReviewed = Review::where('item_id', $item->id)
            ->where('reviewer_id', $user->id)
            ->exists();
        $showModal = ($item->status === 'sold' && !$alreadyReviewed);

        return view('items.chat', compact('item', 'messages', 'otherItems', 'showModal'));
    }
    // メッセージ追加
    public function store(ChatRequest $request, Item $item)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }
        Chat::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'text' => $request->message,
            'image_path' => $imagePath,
            'is_seller' => (Auth::id() === $item->user_id),
        ]);
        return back();
    }
    // メッセージ編集
    public function update(Request $request, Chat $chat)
    {
        if (Auth::id() !== $chat->user_id) {
            abort(403);
        }
        $request->validate(['message' => 'required|string|max:400']);
        $chat->update([
            'text' => $request->message,
            'is_edited' => true
        ]);
        return back()->with('message', 'メッセージを更新しました');
    }
    // メッセージ削除
    public function destroy(Chat $chat)
    {
        if (Auth::id() !== $chat->user_id) {
            abort(403);
        }
        $chat->delete();
        return back()->with('message', 'メッセージを削除しました');
    }
    // 評価保存とユーザーテーブルの評価更新
    public function storeReview(Request $request, Item $item)
    {
        $request->validate(['rating' => 'required|integer|between:1,5']);
        $reviewer_id = Auth::id();
        $reviewed_id = ($reviewer_id === $item->user_id) ? $item->buyer_id : $item->user_id;
        DB::transaction(function () use ($item, $reviewer_id, $reviewed_id, $request) {
            Review::create([
                'item_id' => $item->id,
                'reviewer_id' => $reviewer_id,
                'reviewed_id' => $reviewed_id,
                'rating' => $request->rating,
            ]);
            $user = User::lockForUpdate()->find($reviewed_id);
            $oldCount = $user->evaluation_count;
            $oldEvaluation = $user->evaluation;
            $newCount = $oldCount + 1;
            $newEvaluation = (($oldEvaluation * $oldCount) + $request->rating) / $newCount;
            $user->update([
                'evaluation' => round($newEvaluation, 1),
                'evaluation_count' => $newCount,
            ]);
        });
        return redirect()->route('items.index')->with('message', '評価を送信しました。ご利用ありがとうございました！');
    }
    // 取引完了→評価
    public function complete(Item $item)
    {
        if (Auth::id() !== $item->buyer_id) {
            abort(403);
        }
        $item->update([
            'status' => 'sold'
        ]);
        Mail::to($item->user->email)->send(new TransactionCompleted($item));
        return back()->with('message', '取引を完了しました。評価をお願いします。');
    }
}
