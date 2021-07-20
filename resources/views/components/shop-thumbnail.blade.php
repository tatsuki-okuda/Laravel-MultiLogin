<div>
    @if ( empty($filname) )
        <img src="{{ asset('images/no_image.jpg') }}" alt="">
    @else
        <img src="{{ asset( 'storage/shops/'.$filname ) }}" alt="">
    @endif
</div>