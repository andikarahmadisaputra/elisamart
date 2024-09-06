@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('master.tag.show.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('tags.index') }}"> {{ __('master.tag.button.cancel') }}</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ __('master.tag.form.name') }}:</strong>
            {{ $tag->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ __('master.tag.form.detail') }}:</strong>
            {{ $tag->detail }}
        </div>
    </div>
</div>
@endsection