<x-tests.app>
    <x-slot name="header" >header1</x-slot>
    コンポーネント1
    <x-tests.card title="タイトル" content="コンテンツ"  :message="$message"/>
    <x-tests.card title="cssの変更" class="bg-red-300" />
</x-tests.app>
