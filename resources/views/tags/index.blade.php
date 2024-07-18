@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Tags</h2>
        </div>
        <div class="pull-right">
            @can('tag-create')
            <a class="btn btn-success btn-sm mb-2" href="{{ route('tags.create') }}"><i class="fa fa-plus"></i> Create New Tag</a>
            @endcan
        </div>
    </div>
</div>

@session('success')
    <div class="alert alert-success" role="alert"> 
        {{ $value }}
    </div>
@endsession

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Details</th>
        <th width="280px">Action</th>
    </tr>
    @foreach ($tags as $tag)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $tag->name }}</td>
        <td>{{ $tag->detail }}</td>
        <td>
            <form action="{{ route('tags.destroy',$tag->id) }}" method="POST">
                <a class="btn btn-info btn-sm" href="{{ route('tags.show',$tag->id) }}"><i class="fa-solid fa-list"></i> Show</a>
                @can('tag-edit')
                <a class="btn btn-primary btn-sm" href="{{ route('tags.edit',$tag->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                @endcan

                @csrf
                @method('DELETE')

                @can('tag-delete')
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                @endcan
            </form>
        </td>
    </tr>
    @endforeach
</table>

{!! $tags->links() !!}

<p class="text-center text-primary"><small>Tutorial by ItSolutionStuff.com</small></p>
@endsection