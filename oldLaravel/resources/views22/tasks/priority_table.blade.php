@php 
    //dd($priority); 
@endphp
@if($priority)
    @php    $title = "Prioridades";    @endphp
    @include("tasks.data_table_head") 
    @foreach($priority as $item)
        @include("tasks.data_table_body")
    @endforeach
    @include("tasks.data_table_footer") 
@endif