@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="d-flex justify-content-between mb-2">
        <div>
            <h2>{{ __('master.user.create.title') }}</h2>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}"><i class="bi-x-circle"></i> {{ __('master.user.button.cancel') }}</a>
        </div>
    </div>
</div>

{{-- Display Error Messages --}}
@if ($errors->any())
    <div class="alert alert-danger">
      <strong>{{ __('Whoops! Something went wrong.') }}</strong>
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
        {{-- Name Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="name"><strong>{{ __('master.user.form.name') }}:</strong></label>
                <input type="text" id="name" name="name" placeholder="Name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Email Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="email"><strong>{{ __('master.user.form.email') }}:</strong></label>
                <input type="email" id="email" name="email" placeholder="Email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="password"><strong>{{ __('master.user.form.password') }}:</strong></label>
                <input type="password" id="password" name="password" placeholder="Password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Confirm Password Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="confirm-password"><strong>{{ __('master.user.form.confirm_password') }}:</strong></label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password"
                       class="form-control">
            </div>
        </div>

        @can('user.assign_role')
        {{-- Role Selection --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="roles"><strong>{{ __('master.user.form.role') }}:</strong><span class="text-muted">(Optional)</span></label>
                <select name="roles[]" id="roles" class="form-control @error('roles') is-invalid @enderror" multiple>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" {{ collect(old('roles'))->contains($value) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('roles')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        @endcan

        {{-- Username Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="nia"><strong>{{ __('master.user.form.username') }}:</strong><span class="text-muted">(Optional)</span></label>
                <input type="text" id="username" name="username" placeholder="Username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}">
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- NIK Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="nik"><strong>{{ __('master.user.form.nik') }}:</strong><span class="text-muted">(Optional)</span></label>
                <input type="text" id="nik" name="nik" placeholder="NIK"
                       class="form-control @error('nik') is-invalid @enderror"
                       value="{{ old('nik') }}">
                @error('nik')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Gender Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label><strong>{{ __('master.user.form.gender') }}:</strong><span class="text-muted">(Optional)</span></label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="pria"
                               {{ old('gender') == 'pria' ? 'checked' : '' }}>
                        <label class="form-check-label" for="male">{{ __('Pria') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="wanita"
                               {{ old('gender') == 'wanita' ? 'checked' : '' }}>
                        <label class="form-check-label" for="female">{{ __('Wanita') }}</label>
                    </div>
                </div>
                @error('gender')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Phone Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="phone"><strong>{{ __('master.user.form.phone') }}:</strong><span class="text-muted">(Optional)</span></label>
                <input type="text" id="phone" name="phone" placeholder="Phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- PIN Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label for="pin"><strong>{{ __('master.user.form.pin') }}:</strong><span class="text-muted">(Optional)</span></label>
                <input type="text" id="pin" name="pin" placeholder="PIN"
                       class="form-control @error('pin') is-invalid @enderror">
                @error('pin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Tags Input --}}
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label><strong>{{ __('master.user.form.tag') }}:</strong><span class="text-muted">(Optional)</span></label>
                <div>
                    @foreach($tags as $tag)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tags[]"
                                   id="tag_{{ $tag->id }}" value="{{ $tag->id }}"
                                   {{ collect(old('tags'))->contains($tag->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('tags')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">
                <i class="bi-save"></i> {{ __('master.user.button.submit') }}
            </button>
        </div>
    </div>
</form>
@endsection