@extends('dimages::layout')

@section('content')


<h2>Upload an image</h2>
<ul>
@foreach ($dimages as $dim)
    <li>{{ $dim->filename }} </li>
@endforeach
</ul>
<form action="{!!route('dimages-store')!!}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="dimage">Select an image to Upload</label>
                <input type="file" class="form-control" name="dimage" id="dimage" aria-describedby="dimageHelp" placeholder="Browse">
                <small id="dimageHelp" class="form-text text-muted">Select an image file to upload.</small>
            </div>
            <button class="btn btn-primary" type="submit">Subir Archivo</button>
        </div>
    </div>
</form>

<form action="{!!route('dimages-attach')!!}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="domain">Domain</label>
                <input type="text" required class="form-control" name="domain" id="domain" aria-describedby="domainHelp" placeholder="eg: Videogames">
                <small id="domainHelp" class="form-text text-muted">Domain of all the images above.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" required class="form-control" name="slug" id="slug" aria-describedby="slugHelp" placeholder="eg: Super Mario Odyssey">
                <small id="domainHelp" class="form-text text-muted">Identity of all the images above.</small>
            </div>
        </div>
    </div>
    <button class="btn btn-primary btn-lg" type="submit">Guardar Imagenes</button>
    
</form>

@endsection