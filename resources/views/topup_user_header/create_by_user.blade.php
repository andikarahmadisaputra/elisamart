@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.topup_user.create_by_user.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('topup_user.index') }}"><i class="fa fa-arrow-left"></i> {{ __('transaction.topup_user.button.cancel') }}</a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('topup_user.store_by_user') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_user.form.store') }}:</strong>
                <select class="form-select" name="store_id">
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_user.form.user') }}:</strong>
                <input type="text" name="user" class="form-control" placeholder="{{ __('transaction.topup_user.form.user') }}" value="{{ old('user') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_user.form.amount') }}:</strong>
                <input type="text" name="amount" class="form-control" placeholder="{{ __('transaction.topup_user.form.amount') }}" value="{{ old('amount') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_user.form.note') }}:</strong>
                <input type="text" name="note" class="form-control" placeholder="{{ __('transaction.topup_user.form.note') }}" value="{{ old('note') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mb-3 mt-2"><i class="fa-solid fa-floppy-disk"></i> {{ __('transaction.topup_user.button.submit') }}</button>
        </div>
    </div>
</form>
@endsection