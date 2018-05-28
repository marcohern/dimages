@extends('dimages::layout')

@section('content')


<h2>Upload an image</h2>
<ul>
@foreach ($dimages as $dim)
    <li>{{ $dim->filename }} </li>
@endforeach
</ul>
<form action="{{route('dimages-store')}}" method="post" enctype="multipart/form-data">
    
    @csrf
    
    <div class="form-group">
        <label for="dimage">Select an image to Upload</label>
        <input type="file" class="form-control" name="dimage" id="dimage" aria-describedby="emailHelp" placeholder="Browse">
        <small id="emailHelp" class="form-text text-muted">Select an image file to upload.</small>
    </div>

    <button class="btn btn-primary" type="submit">Enviar</button>
</form>

@endsection