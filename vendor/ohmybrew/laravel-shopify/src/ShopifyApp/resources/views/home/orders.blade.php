@extends('shopify-app::layouts.default')

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <link href="{!! asset('dataTable/jquery.dataTables.css') !!}" media="all" rel="stylesheet" type="text/css" />

    <style>

    </style>
@endsection

@section('content')
    <div class="position-ref full-height">
      <div class="cpl-md-12">
          <h3>All Orders</h3>
      </div>
      <div class="cpl-md-12">

<table id="employee-grid" class="table table-striped table-bordered table-hover" >
   <thead>
    <tr>
    <th>Order Number</th>
    <th>Email</th>
    <th>Total Price</th>
    <th>Requires shipping</th>
    <th>type</th>
    <th>Note</th>
    <th>Action</th>
    </tr>
  </thead>
  <tbody>

    </tbody>
  </table>
      </div>


    </div>
    @stop

    @section('scripts')
      <script type="text/javascript" src="{!! asset('dataTable/jquery.dataTables.js') !!}"></script>
      <script>

      $(document).ready(function() {

table = $('#employee-grid').DataTable({
              "lengthMenu": [[100, 500, 1000, 2000, 3000, -1], [100, 500, 1000, 2000, 3000, "All"]],
             // dom: 'lBfrtip',
              "serverSide": true,
              "searching":false, //Feature control DataTables' server-side processing mode.
              //"order": [], //Initial no order.
              // Load data for the table's content from an Ajax source
              ajax: {
                    "url": "{{url('PendingOrderAll')}}?{{$shop->domain}}",
                      "type": "POST",
                      "data": function ( d ) {
                     d.shop = "{{$shop->domain}}";
                    }

              },


      });
    });
    function placeDelivery(orderId)
    {
      console.log(orderId);
      $.ajax({
         type:'POST',
         url:"{{url('PlaceDelivery?shop=').$shop->domain}}",
         data:{shop:'{{$shop->domain}}',orderId:orderId},
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
    }
      </script>
    <script>
    $(document).ready(function() {
   });
    </script>
    @endsection
