@extends('shopify-app::layouts.default')

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <style>

    </style>
@endsection

@section('content')
    <div class="flex-center position-ref full-height">
      <div class="container">
        <div id="loginbox" style="" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Ninga Api Credentials</div>
                    </div>
                    <div style="padding-top:30px" class="panel-body" >
                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                        <form id="loginform" class="form-horizontal" role="form">
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="login-email" type="text" class="form-control" name="email" value="" placeholder="username or email">
                                    </div>
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                                    </div>
                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->
                                    <div class="col-sm-12 controls">
                                      <button id="btn-login" type="submit" class="btn btn-success">Validate  </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                  </div>
        </div>
    </div>
    @stop

    @section('scripts')
    <script>
    $(document).ready(function() {
      alert('test');
    $("#loginform").on('submit',function(e){
        e.preventDefault();
        var error=0;
          var form = $('#loginform')[0];
          var data = new FormData(form);
        $.ajax({
           type:'POST',
           url:"{{url('NinjaAuthLogin?shop=').$shop->domain}}",
           cache: false,
           contentType : false,
           processData : false,
           data:data,
           dataType:'json',
           success:function(data)
           {
             if(data['code']==200)
             {
                ShopifyApp.flashNotice(data['msg']);
                location.reload();
             }
             else
             {
                ShopifyApp.flashError(data['msg']);
             }
           }
        });

    });
  });
    </script>
    @endsection
