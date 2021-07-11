@props([
    'title',
    'message' => '初期値です',
    'content' => 'コンテンツ初期値'
])
<div {{$attributes->merge([
    'class' => 'order-2 shadow-md w-1/4 p-2'
])}} >
    <div>{{ $title }}</div>
    <div>画像</div>
    <div>{{ $content }}</div>
    <div>{{ $message }}</div>
</div>