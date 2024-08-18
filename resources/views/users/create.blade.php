@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('master.user.create.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('users.index') }}"><i class="fa fa-arrow-left"></i> {{ __('master.user.button.cancel') }}</a>
        </div>
    </div>
</div>

@if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Whoops!</strong> There were some problems with your input.<br><br>
      <ul>
         @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
         @endforeach
      </ul>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.name') }}:</strong>
                <input type="text" name="name" placeholder="Name" class="form-control" value="{{ old('name') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.email') }}:</strong>
                <input type="email" name="email" placeholder="Email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.password') }}:</strong>
                <input type="password" name="password" placeholder="Password" class="form-control" value="{{ old('password') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.confirm_password') }}:</strong>
                <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.role') }}:</strong>
                <select name="roles[]" class="form-control" multiple="multiple">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                     @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.nik') }}:</strong>
                <input type="text" name="nik" placeholder="NIK" class="form-control" value="{{ old('nik') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.gender') }}:</strong>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="pria" required>
                        <label class="form-check-label" for="male">Pria</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="wanita" required>
                        <label class="form-check-label" for="female">Wanita</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.pin') }}:</strong>
                <input type="text" name="pin" placeholder="PIN" class="form-control" value="{{ old('pin') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('master.user.form.tag') }}:</strong>
                <div>
                    @foreach($tags as $tag)
                        <input
                            class="form-check_input"
                            type="checkbox"
                            name="tags[]"
                            id="tag_{{ $tag->id }}"
                            value="{{ $tag->id }}">
                        <label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> {{ __('master.user.button.submit') }}</button>
        </div>
    </div>
</form>
@endsection