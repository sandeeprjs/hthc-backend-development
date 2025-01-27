@extends('layouts.app')

@section('auth-content')

<div class="container vh-100 login-container">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-5">

            <div class="w-100 text-center">
                <img src="{{URL::to('/')}}/images/logo.png" />
                <h2>Login to HTHC</h2>
            </div>

            <div class="login-card">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email" >{{ __('E-Mail / Employee Code') }}</label>

                            <div class="">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>

                            <div class="">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

{{--                        <div class="form-group row">--}}
{{--                            <div class="">--}}
{{--                                <div class="form-check">--}}
{{--                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}

{{--                                    <label class="form-check-label" for="remember">--}}
{{--                                        {{ __('Remember Me') }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <div class="">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
{{--                        <div class="form-group">--}}
{{--                            <button type="button" onclick="location.href='https://hthc.co.in/HTHC_v1.0.2.apk';" class="btn btn-link">Download APK</button>--}}
{{--                        </div>--}}
                    </form>
                </div>
            </div>
              <div class="w-100 text-center">
               <!-- Footer -->
                <footer class="page-footer font-small blue">

                <!-- Copyright -->
                <div class="footer-copyright text-center py-3">
                    <a href="https://www.netiapps.com/" target="_blank">Developed & Maintained by Aviskara Solutions</a>
                </div>
                <!-- Copyright -->

               </footer>
             <!-- Footer -->
        </div>
        </div>
    </div>
</div>
@endsection
