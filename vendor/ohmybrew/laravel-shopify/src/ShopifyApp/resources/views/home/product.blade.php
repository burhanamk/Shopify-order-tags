@extends('shopify-app::layouts.default')

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <style>
        html, body {
            background-color: #fff;
            color: #000;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }
        .full-height {
            height: 100vh;
        }
        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }
        .position-ref {
            position: relative;
        }
        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }
        .content {
            text-align: center;
        }
        .title {
            font-size: 84px;
        }
        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
        .m-b-md {
            margin-bottom: 30px;
        }
        ul{list-style: none;}
        li{font-weight: bold;}
        span{font-weight: bold;}
        .btn{font-weight: bold;}
        label{font-weight: bold;}
        input{font-weight: bold;}
        .container{margin-top: 40px;}
        .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.disableData{display: none;}
    </style>
@stop

@section('content')
    <div class="flex-center position-ref">

        <div class="container">

        <div class="row">
            <div class="col-md-12">
               <div class="col-md-12" style="padding-bottom: 20px">
                 <button type="button" class="btn btn-info btn-sm" onclick="backproduct();">Back</button>
             </div>
             <div class="col-md-12">
               <h>Enable Custom Height Width</h>
               <label class="switch">
              <input type="checkbox" id="customwh" onchange="makeCustomHw();" >
              <span class="slider round"></span>
              </label>
              </div>

            </div>
            <div class="col-m-12">
              <div class="form-group">
                  <label for="cps">Setup Charge:</label>
                  <input type="text" name="setucharge" value="@if(isset($prddata->setup_charge)){{$prddata->setup_charge}}@endif" placeholder="Enter setup charge" id="setupc" class="form-control" id="setup">
              </div>
              <div class="form-group">
                  <button type="button" class="btn btn-info btn-sm"  id="setupButton">Update Setup Charge</button>
              </div>
            </div>

        <div class="col-md-12">
             @if(count($size_set) > 0)
            <span>Available Size</span>

        <ul>
       @foreach($size_set as $single_size_set)
        <li><b>Size Set</b>: {{$single_size_set->height}} X {{$single_size_set->width}}</li>

        @endforeach
        </ul>
        @else
        <span>No Size Set Available</span>
        @endif


        <ul>
        <li>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#size_set_model">Update Size Set</button>
        </li>
        </ul>
        </div>
        <div class="col-md-12">
             @if(count($quantity_set) > 0)
            <span>Quantity Set</span>
        <ul>
        @foreach($quantity_set as $single_quantity_set)
        <li><b>Quantity</b>: {{$single_quantity_set->quantity_set}}</li>
        @endforeach
        </ul>
        @else
         <span>No Quantity Set Available</span>
        @endif

        <ul>
        <li>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">Update Quantity Set</button>
        </li>
        </ul>

        </div>

        <div class="col-md-12">
          @if(count($production_set) > 0)
         <span>Production time Set</span>
     <ul>
     @foreach($production_set as $single_production_set)
     <li><b>Title</b>:
        {{$single_production_set->option_title}}
        @if($single_production_set->option_type==0)
        Percent:
        @else
         Price
         @endif
         {{$single_production_set->option_value}}
         @if($single_production_set->is_disable==1)
         Disable When

         @if($single_production_set->option_when==1)
         Price
         @else
         SF
         @endif
         > {{$single_production_set->greater_value}}


         @endif
       </li>
     @endforeach
     </ul>
     @else
      <span>No Production Set Available</span>
     @endif
          <ul>
          <li>
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#Production_modal">Update Production Time</button>
          </li>
          </ul>
        </div>

        <div class="col-md-12">
          @if(count($shipping_set) > 0)
         <span>Shipping time Set</span>
     <ul>
     @foreach($shipping_set as $single_shipping_set)
     <li><b>Title</b>:
        {{$single_shipping_set->option_title}}
        @if($single_shipping_set->option_type==0)
        Percent:
        @else
         Price
         @endif
         {{$single_shipping_set->option_value}}
       </li>
     @endforeach
     </ul>
     @else
      <span>No Shipping Set Available</span>
     @endif
          <ul>
          <li>
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#Shipping_modal">Update Shipping Time</button>
          </li>
          </ul>
        </div>



        </div>
    </div>


    <!--    Shipping_modal-->
    <div class="modal fade" id="Shipping_modal" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Update Shipping Time</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

          </div>
          <div class="modal-body">
          <form method="post" id="shipping_setForm">
            <div class="col-md-12">
               <div class="form-group">
                   <label for="setup_charge">Option title:</label>
                   <input type="text" name="option_title"  class="form-control" id="ship_title">
               </div>
           </div>
           <div class="col-md-12">
              <div class="form-group">
                  <label for="setup_charge">Option Days:</label>
                <select name="option_days">
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                </select>


              </div>
          </div>
          <div class="col-md-12">
             <div class="form-group">
                 <label for="setup_charge">Option Type:</label>
               <select name="option_type">
                 <option value="0">Percent %</option>
                 <option value="1">Price $</option>
               </select>


             </div>
         </div>
         <div class="col-md-12">
            <div class="form-group">
                <label for="setup_charge">Option Value:</label>
                <input type="text" name="option_value"  class="form-control" id="opt_value">
            </div>
        </div>



           <div class="col-md-12">
              <div class="form-group">
               <button type="submit" class="btn btn-info btn-md" >Add</button>
              </div>
          </div>
          </form>


          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

<!--    Production_modal-->
<div class="modal fade" id="Production_modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">Update Production Time</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>
      <div class="modal-body">
      <form method="post" id="production_setForm">
        <div class="col-md-12">
           <div class="form-group">
               <label for="setup_charge">Option title:</label>
               <input type="text" name="option_title"  class="form-control" id="opt_title">
           </div>
       </div>
       <div class="col-md-12">
          <div class="form-group">
              <label for="setup_charge">Option Days:</label>
            <select name="option_days">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
            </select>


          </div>
      </div>
      <div class="col-md-12">
         <div class="form-group">
             <label for="setup_charge">Option Type:</label>
           <select name="option_type">
             <option value="0">Percent %</option>
             <option value="1">Price $</option>
           </select>


         </div>
     </div>
     <div class="col-md-12">
        <div class="form-group">
            <label for="setup_charge">Option Value:</label>
            <input type="text" name="option_value"  class="form-control" id="opt_value">
        </div>
    </div>
    <div class="col-md-12">
       <div class="form-group">
           <label for="setup_charge">Is Disable :</label>
           <input type="Checkbox" value="1" name="is_disable"  class="form-control" id="is_disable">
       </div>
       <div class="form-group disableData">
           <label for="setup_charge">When :</label>
           <select name="option_when">
             <option value="1">Price ></option>
             <option value="2">SF ></option>

           </select>
       </div>
       <div class="form-group disableData" >
           <label for="setup_charge">Value :</label>
           <input type="text" name="greater_value"  class="form-control" id="greater_value">
       </div>
   </div>


       <div class="col-md-12">
          <div class="form-group">
           <button type="submit" class="btn btn-info btn-md" >Add</button>
          </div>
      </div>
      </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

      <!--Quantity set Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Quantity Set</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">
        <form method="post" id="quantity_setForm">
         <div class="col-md-12">
            <div class="form-group">
                <label for="setup_charge">Quantity:</label>
                <input type="text" name="qtyset"  class="form-control" id="qtyset">
            </div>
        </div>
         <div class="col-md-12">
            <div class="form-group">
             <button type="submit" class="btn btn-info btn-md" >Add</button>
            </div>
        </div>
        </form>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
<!--size set modal-->
   <div class="modal fade" id="size_set_model" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Size Set</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">
        <form method="post" id="size_setForm">
         <div class="col-md-12">
            <div class="form-group">
                <label for="setup_charge">Width:</label>
                <input type="text" name="width"  class="form-control" id="width">
            </div>
        </div>
       <div class="col-md-12">
            <div class="form-group">
                <label for="cps">Height:</label>
                <input type="text" name="height" class="form-control" id="height">
            </div>
        </div>
         <div class="col-md-12">
            <div class="form-group">
             <button type="submit" class="btn btn-info btn-md" >Add</button>
            </div>
        </div>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>

@stop

@section('scripts')

    <script type="text/javascript">
        window.mainPageTitle = 'Main Page';
            ShopifyApp.ready(function(){
                ShopifyApp.Bar.initialize({
                    title: 'Edit Product',
            });
        });

        $(document).ready(function() {
            $('#is_disable').change(function() {
                if($(this).is(":checked")) {
                  $('.disableData').show();

                }
                else
                {
                  $('.disableData').hide();
                }

            });
        });






    function backproduct(){
        ShopifyApp.redirect("/products");
    }

    function makeCustomHw(check)
    {
    if($('#customwh').is(':checked')){
    var is_custom_hw=1;
    }else{
    var is_custom_hw=0;
    }
    $.ajax({
       type:'POST',
       url:"{{url('updatehw?productId=').$productData}}",
       data:{is_custom_hw:is_custom_hw},
       dataType:'json',
       success:function(data)
       {
         if(data['code']==200)
         {

            ShopifyApp.flashNotice("Update successfully.");
            location.reload();
         }
         else
         {
            ShopifyApp.flashError(data['msg']);
         }
       }
    });
    }


      $("#setupButton").on('click',function(e){
          e.preventDefault();

          $.ajax({
             type:'POST',
             url:"{{url('setupData?productId=').$productData}}",
             data:{setupcharge:$('#setupc').val()},
             dataType:'json',
             success:function(data)
             {
               if(data['code']==200)
               {

                  ShopifyApp.flashNotice("Update successfully.");
                  location.reload();
               }
               else
               {
                  ShopifyApp.flashError(data['msg']);
               }
             }
          });
      });

//Shipping modal FormData

$("#shipping_setForm").on('submit',function(e){
    e.preventDefault();
    var error=0;
      var form = $('#shipping_setForm')[0];
      var data = new FormData(form);


    $.ajax({
       type:'POST',
       url:"{{url('shipping_set_update?productId=').$productData}}",
       cache: false,
       contentType : false,
       processData : false,
       data:data,
       dataType:'json',
       success:function(data)
       {
         if(data['code']==200)
         {

            ShopifyApp.flashNotice("Update successfully.");
            location.reload();
         }
         else
         {
            ShopifyApp.flashError(data['msg']);
         }
       }
    });

});





//production_setForm

$("#production_setForm").on('submit',function(e){
    e.preventDefault();
    var error=0;
      var form = $('#production_setForm')[0];
      var data = new FormData(form);
    var is_disable=  $('#is_disable:checkbox:checked').length > 0;
    if(is_disable)
    {
      var greater_value = $('#greater_value').val();
      if(greater_value=='')
      {
        error++;
        ShopifyApp.flashError("Please enter value for disable.");
      }
    }
    if(error==0){
    $.ajax({
       type:'POST',
       url:"{{url('production_set_update?productId=').$productData}}",
       cache: false,
       contentType : false,
       processData : false,
       data:data,
       dataType:'json',
       success:function(data)
       {
         if(data['code']==200)
         {

            ShopifyApp.flashNotice("Update successfully.");
            location.reload();
         }
         else
         {
            ShopifyApp.flashError(data['msg']);
         }
       }
    });
  }
});
      $("#size_setForm").on('submit',function(e){
          e.preventDefault();
            var form = $('#size_setForm')[0];
            var data = new FormData(form);
          $.ajax({
             type:'POST',
             url:"{{url('size_set_update?productId=').$productData}}",
             cache: false,
             contentType : false,
             processData : false,
             data:data,
             dataType:'json',
             success:function(data)
             {
               if(data['code']==200)
               {

                  ShopifyApp.flashNotice("Update successfully.");
                  location.reload();
               }
               else
               {
                  ShopifyApp.flashError(data['msg']);
               }
             }
          });
      });



      $("#quantity_setForm").on('submit',function(e){
          e.preventDefault();
            var form = $('#quantity_setForm')[0];
            var data = new FormData(form);
          $.ajax({
             type:'POST',
             url:"{{url('quantity_set_update?productId=').$productData}}",
             cache: false,
             contentType : false,
             processData : false,
             data:data,
             dataType:'json',
             success:function(data)
             {
               if(data['code']==200)
               {

                  ShopifyApp.flashNotice("Update successfully.");
                  location.reload();
               }
               else
               {
                  ShopifyApp.flashError(data['msg']);
               }
             }
          });
      });

    </script>



@endsection
