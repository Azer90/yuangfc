<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style>


    @media screen and (min-width: 800px) {
      .login-box-body {
        border-radius: 4px;
        padding: 50px 40px 38px;
        position: absolute;
        width: 360px;
      }

    }

    @media screen and (max-width: 640px) {
      .scanCode {
        display: none;
      }
      .bg_img {
        display: none;
      }
      .tips {
        display: none !important;
      }
    }
    .bg_img {
      width: 60px;
      height: 60px;
      background-image: url('data:image/png;base64,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');
      background-size: 120px 120px;
      background-repeat: no-repeat;
      background-position: 0 0;
      position: absolute;
      top: 5px;
      right: 5px;
    }
    .bg_img:hover {
      background-position: -60px 0;
      cursor: pointer;
    }
    .qrCode {
      text-align: center;
      padding-top: 20px;
    }
    .scanCode .titles {
      font-size: 20px;
      margin-top: 25px;
      color: #444;
      text-align: center;
    }
    .list_scan {
      width: 128px;
      margin: 0 auto;
      margin-top: 15px;
    }
    .list_scan>img {
      width: 40px;
      height: 40px;
      float: left;
      margin-right: 15px;
    }
    .list_scan span {
      display: inline-block;
      font-size: 13px;
      margin-bottom: 2px;
    }

    .list_scan .weChatSamll img {
      width: 100%;
    }
    .list_scan .weChatSamll em {
      position: absolute;
      border: 7px solid #ececec;
      border-color: #ececec #00000000 #00000000 #00000000;
      width: 0;
      height: 0;
      right: 87px;
      bottom: -14px;
      margin-left: -6px;
    }
    .tips {
      position: absolute;
      top: 10px;
      right: 65px;
      color: rgb(32, 165, 58);
      background: #dff0d8;
      padding: 5px 10px;
      text-align: center;
      border-radius: 4px;
    }
    .tips em {
      position: absolute;
      border: 6px solid #dff0d8;
      border-color: #00000000 #00000000 #00000000 #dff0d8;
      width: 0;
      height: 0;
      right: -11px;
      top: 8px;
      margin-left: -6px;
    }
    .tips img {
      height: 16px;
      width: 16px;
      vertical-align: middle;
      margin-top: -1px;
      margin-right: 4px;
    }


  </style>
</head>
<body class="hold-transition login-page" @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
<div class="login-box">
  <div class="login-logo">
    <a href="{{ admin_base_path('/') }}"><b>{{config('admin.name')}}</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <div class="scanCode" style="display: none">
      <div class="titles">
            <span>扫码登录</span>
      </div>
      <div class="qrCode" id="qrcode">
        <img width="160" src="data:image/png;base64,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" />
      </div>
      <div class="scanTip">
        <div class="list_scan">
          <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAEtElEQVR4nO3dzW4bVRgG4Pcbt27igvAChMSCnj2IGiEku5s6cAHMNnWrNGzZhCsguQLCFdghdrLEiD2xN4klhEgkWHfCAomfRbrBqannsBg3tT0/nhn/cIjfR8oiZ8aTo3lz5syM7fkAIiIiIpo9mXYDqlrI49bqXQCAlvLUPXrBcvec9Y4zs+0NUfvFMizr/sw2KLoFAHjWPXM2Ty+m2dSNNC9S1UIe2dUNaNgQKUNP04UQz9EGMJdAYFllAF/MbHt68H+dzUHVSy0Imuh199KEYyV9gaoXN3Az9wSQXcgMR8R1IVIGZBfZ1Z9UvbiR9OWxR8hgVFQBsZP+keUkCiI11SjZ6HU3446WWCPECyN3xDDSEBvZ3JGqFvKx1o6zkqqXjsIPT7oN6Bp6l81pJ7T/K3VYVOiLDYENSPDJgtYt5+HJ2qRtTQxENe5tI2gC1DiDdrecR51WjD4vDdUoPgZkF5DXAhbvOJXj7ajXRwaiDosKrvUkYNG36P39eFlHxCTeiLGaENwdWaBxgYz7ftTpfPQc0hd/mhpnDCOas95xkHFtQD8dWSDIB+7TIRMmdfnE15RxbYYxmbPeceDqgJOggH06JDQQdVCyIRg/M9iZ19XzdeTNr7o90ijIq/1i6PVb+AjRUvCv7dam6N+S0v595t0pCJTgSl2fc3Sk0LtsJlk9IhA9niLDSMGbb8cmd/++vZLkXtZpyj5Rgn2XIBDhmdUCJL7bS/PFQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAzDQAwT9WHrU4x8z0DzLdz0xvYl330lIiIiIiKiEIFfaXv74MN3rH7mjUV3Zpm4mf6fvz744Zfx9pFA3vz6vdsrmVe+E2Dit0Vpehr4/rxy/PFw28jNxVtW7lOGsTgCfHSnXvpsuM0a/UVuL7ZLJMCrw7+PBPI8i31A/77YLi0xjT+07u0NN/km9bcOPng927/57uJ6tbx6mX9+/u3Bj3/91/0gIiIiIqLlFvoAM9UotUYfV6fbTuWETyFNIcm+5CcXDcNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADMNADBMRiLDY18wEFggLlORh/Pfj1uKjl9RhUQEYL44T+qyTiEBc/8NmsissLJmUi4APM4RXmggPJLAyjMyuoO/SCNxnoVV3QgNxNk8voHE2tnGl6qWt1H1bMl5RTlGjrfrcqRynOWQBEHfX3yZfRlUZI49X8TOoRLhEFlaLUXp1/ENeL7ibTqXDqm0BvDCsqn+JPncqJ8rf/lKMsyzZ8he1AgCrqhqlbwZnEQRANe4VVL10FBwGAMHEw328atGhiV85BdAE9AVkRo+ve9Y9m1dFUXVYVNByZyYb0yhAQw2qaftrP16tpz93Hp74p4AxsQrcO5VOTTWKiAil4P3I2KMFp3BjZQ3AfC5OXSvk+J7S5H/rnThhAAkuDJ1KpwbXXQs+fFEw/dSba6NLdg9LdC/LedRpoddVAHYYzCT6K/S6KumJT6xD1rDBcX0bwLY6KNlwYUMwuD0Q/57N9aPb3hW420TvssmK2kREREQm+RdN1VmzIpYfDgAAAABJRU5ErkJggg=="/>
          <span> 打开 <a class="btlink" href="javascript:;"> 微信 </a> </span> <span> 扫一扫登录 </span>
        </div>
      </div>
    </div>

    <div class="entrance">
      <div class="bg_img"> </div>
      <div class="tips">
        <span> <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAACjklEQVRYhe2XTVLbQBBGX4ufCt7gG2RyArTE3mBuYLY4KcwJ8E0iTmBTQckS5wSIDWYX5wQoN7A3pgoKdRZjbGyNLCkmq6RXqpqe+d50T4964F83KeJkvuw38LwDVKuAb2fK/BuGqI5m3yIjRKP4eHDzxwBT0ROUJkI17aG/gPjFHeR92oURQp8kuYg/3UWFAExY90GvQMwroT5ChEoct26Hq3ZkwrqPqEFpAM05mMYgR8vzHQC1COQA1QtEgjzBPDNh3Ue1g8gJ6E3cGjRyJtQiE9Z1HdEMELWbWzTP7a7jtwbIMgeAHABrhd1teoPKXgGA9c182zem66crx1FNbw5gwrrPs/eD7UqviP+bAtgTzzVCFdFCAJt/RZzkND6+6xeZVzgCzpxmibfuCu2+MIAJ6322Kvf2llxHPF3eDgBHuYj2EKoo168hyom7y9sdgaVyiY8HfUhOX0OsE/YcABlBOudWYA5RRny+loyWx1wRsGHafJfK9wJEmZ3P1yqSgsT+40VSADOIJDkkSQ4Lh322VhIvD6XvAY+IBBBpAIETIqO5WAHQmK29POTyN2EtRmWXp8mH+HSYylsZM12/ylblHtFx3BqY5fGMe0Bs2W1XPq8jDsDWTjA9uM5oZveEl/Uhwt46JWbC/TZ4XZSf8cdb55nKvgk3kqa9ubyuCWtnpcUvax3wuqBju5bbVrbl0wY1AtlFNeLp4SjvTNic71zZg6djkMaqvjL3XWC6fpXtnb5tVBkhBDxOzpdBrF/lDKUzvUm/8zhp5wEXepjASz4lsNFgBNpjQ88BeJYzkLYV1jFop+i5KQwAs112QDsgu4ujOgYJeJwEZUq3FMACzNdaE5W2XUV79of138rbb3pjLc503PZxAAAAAElFTkSuQmCC" /> <span v-text="type=='qrCode' ? '点击密码登录' : '扫码登录更安全'">扫码登录更安全</span>  </span> <em> </em>
      </div>
    </div>
    <div class="login_pc">
    <p class="login-box-msg">密码{{ trans('admin.login') }}</p>

    <form action="{{ admin_base_path('auth/login') }}" method="post">
      <div class="form-group has-feedback {!! !$errors->has('username') ?: 'has-error' !!}">

        @if($errors->has('username'))
          @foreach($errors->get('username') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif

        <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}" name="username" value="{{ old('username') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">

        @if($errors->has('password'))
          @foreach($errors->get('password') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif

        <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          @if(config('admin.auth.remember'))
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
              {{ trans('admin.remember_me') }}
            </label>
          </div>
          @endif
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('admin.login') }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    </div>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js")}} "></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}"></script>
<script>

  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
    $('.entrance').click(function(){
      if($('.scanCode').is(':hidden')){
        $('.scanCode').show();
        $('.login_pc').hide();

      }else {
        $('.scanCode').hide();
        $('.login_pc').show();

      }
    });
  });

</script>
</body>
</html>
