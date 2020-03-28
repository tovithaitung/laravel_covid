<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login</title>
    <meta name="description" content="Neat">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">

    <!-- Favicon -->
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="shortcut icon" href="{{ asset('stockhomevn/favicon.ico')}}" type="image/x-icon">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('stockhomevn/css/neat.min.css?v=1.0')}}">
  </head>
  <body>

    <div class="o-page o-page--center">
      <div class="o-page__card">
        <div class="c-card c-card--center">
          <span class="c-icon c-icon--large u-mb-small">
            <img src="{{ asset('stockhomevn/img/logo-small.svg')}}" alt="Neat">
          </span>

          <h4 class="u-mb-medium">Welcome Back :)</h4>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="c-field">
                <label class="c-field__label">Email Address</label>
                <input class="c-input u-mb-small" type="text" name="email" placeholder="e.g. adam@sandler.com" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            
            <div class="c-field">
              <label class="c-field__label">Password</label>
              <input class="c-input u-mb-small" type="password" name="password" placeholder="Numbers, Pharagraphs Only" required>
               @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" class="c-btn c-btn--fullwidth c-btn--info">Login</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Main JavaScript -->
    <script src="{{ asset('stockhomevn/js/neat.min.js?v=1.0')}}"></script>
  </body>
</html>