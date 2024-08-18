@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('master.store.show.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('stores.index') }}"> {{ __('master.store.button.cancel') }}</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ __('master.store.form.name') }}:</strong>
            {{ $store->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ __('master.store.form.detail') }}:</strong>
            {{ $store->detail }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ __('master.store.form.balance') }}:</strong>
            {{ sprintf("%.2f", $store->balance) }}
        </div>
    </div>
</div>
@endsection