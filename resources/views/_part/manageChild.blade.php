<ul>
@foreach($children as $child)
   <li>
       {{ $child['text'] }}
        @if($child['children'] != null)
            @include('_part.manageChild',['children' => $child['children']])
        @endif
   </li>
@endforeach
</ul>