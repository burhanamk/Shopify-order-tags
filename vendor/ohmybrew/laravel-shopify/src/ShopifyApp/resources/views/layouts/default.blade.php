<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>{{ config('shopify-app.app_name') }}</title>
  <link rel="stylesheet" href="https://sdks.shopifycdn.com/polaris/latest/polaris.css" />
  <link rel="stylesheet" href="{{ asset('public/css/app.css') }}" />
  <link rel="stylesheet" href="{{ asset('public/css/custom.css') }}" />

<style>


/*# sourceMappingURL=main-1b1ad0be505a52937f1b5a66bde92a07a4572e58a62492278ea729384b4550d6.css.map*/
</style>
    @yield('styles')

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    @if(config('shopify-app.esdk_enabled'))
        <script src="https://cdn.shopify.com/s/assets/external/app.js?{{ date('YmdH') }}"></script>
        <script type="text/javascript">
            ShopifyApp.init({
                apiKey: '{{ config('shopify-app.api_key') }}',
                shopOrigin: 'https://{{ $shop }}',
                debug: false,
                forceRedirect: true
            });
        </script>

        @include('shopify-app::partials.flash_messages')
    @endif


</head>

<body>
  <header id="PageHeader" class="_1aD_c">
    <div class="_1oyfq _3HaIP">
      <div id="navigation" class="LyfXC">
        <nav id="PageNavInterior" class="_2H3Pq" aria-hidden="false" aria-label="Primary navigation">
          <ul class="_1xgvF">
            <li class="_3Qke9 _1itcN">
              <a class="_2Cm-t" aria-current="page" href="{{url('home')}}?shop={{$shop}}">Dashboard</a>
            </li>
            <li class="_3Qke9">
              <a class="_2Cm-t"  href="{{url('inventory')}}?shop={{$shop}}">Inventory</a>
            </li>
            <!-- <li class="_3Qke9"><a class="_2Cm-t" href="/content/product-content">Content</a></li>
            <li class="_3Qke9"><a class="_2Cm-t" href="/design/colors">Design</a></li>
            <li class="_3Qke9"><a class="_2Cm-t" href="/components/get-started">Components</a></li>
            <li class="_3Qke9"><a class="_2Cm-t" href="/patterns/layout">Patterns</a></li> -->
          </ul>
        </nav>
        <!-- <button type="button" class="_3M0wC" aria-label="Show search">
          <span class="_3C3DA _1V8ui _1l1U0">
            <svg class="_1EQ4-" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid" focusable="false" aria-hidden="true">
              <use xlink:href="#SVGIconSearch">
              </use>
            </svg>
          </span>
        </button> -->
        <!-- <div class="_3a7AT" role="search">
        <div class="_2y4XF">
          <div class="_2qnHJ">
              <div class="_3JIaR">
                <span class="_3C3DA _1V8ui _1l1U0">
                  <svg class="_1EQ4-" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid" focusable="false" aria-hidden="true">
                    <use xlink:href="#SVGIconSearch">
                    </use>
                  </svg>
                </span>
              </div>
              <input class="_1En1Z _1KWou" placeholder="Search" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-label="Search in Polaris" value="" type="search">
            </div>
          <button type="button" class="dfUjy">Cancel</button>
        </div>
      </div> -->
    </div>
  </div>
</header>
  <div class="Polaris-Page">
      @yield('content')

      @yield('scripts')
  </div>
</body>

</html>
