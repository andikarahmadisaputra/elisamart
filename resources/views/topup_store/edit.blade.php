@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.topup_store.edit.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('topup_store.index') }}"><i class="fa fa-arrow-left"></i> {{ __('transaction.topup_store.button.cancel') }}</a>
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

<form action="{{ route('topup_store.update',$topupStore->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_store.form.store') }}:</strong>
                <select class="form-select" name="store_id">
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $store->id = $topupStore->store_id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_store.form.amount') }}:</strong>
                <input type="text" name="amount" class="form-control" placeholder="Amount" value="{{ sprintf("%.0f", $topupStore->amount) }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.topup_store.form.note') }}:</strong>
                <input type="text" name="note" class="form-control" placeholder="Note" value="{{ $topupStore->note }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
          <button type="submit" class="btn btn-primary btn-sm mb-2 mt-2"><i class="fa-solid fa-floppy-disk"></i> {{ __('transaction.topup_store.button.submit') }}</button>
        </div>
    </div>
</form>
@endsection