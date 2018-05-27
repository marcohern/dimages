@extends('dimages::layout')

@section('content')


<h2>Upload an image</h2>
<form action="#" method="post" enctype="multipart/form-data">
    {{csrf_field()}}

    
</form>

@endsection