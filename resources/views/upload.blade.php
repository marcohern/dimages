@extends('dimages::layout')

@section('content')


<h2>Upload an image</h2>
<form action="{{route('dimages-store')}}" method="post" enctype="multipart/form-data">
    
    @csrf
    
    <div class="form-group">
        <input type="file" class="form-control" name="dimage" id="dimage" aria-describedby="emailHelp" placeholder="Browse">
        <small id="emailHelp" class="form-text text-muted">Select an image file to upload.</small>
    </div>

    <button class="btn btn-primary" type="submit">Enviar</button>
</form>

@endsection