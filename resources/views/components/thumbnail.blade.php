@php
    if( $type === 'shops' ){
        $path = 'storage/shops/';
    } 
    if( $type === 'products' ){
        $path = 'storage/products/';
    } 
@endphp
<div>
    @if ( empty($filname) )
        <img src="{{ asset('images/no_image.jpg') }}" alt="">
    @else
        <img src="{{ asset( $path.$filname ) }}" alt="">
    @endif
</div>