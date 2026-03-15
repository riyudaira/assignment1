<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <p>{{ $item->user->name }} 様</p>
    <p>出品いただいた商品「{{ $item->name }}」の取引が購入者によって完了されました。</p>
    <p>取引画面より、購入者の評価を行ってください。</p>
    <p>※本メールに心当たりがない場合は、破棄してください。</p>
</body>

</html>
