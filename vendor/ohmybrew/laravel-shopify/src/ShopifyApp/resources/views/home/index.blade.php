@extends('shopify-app::layouts.default')

@section('styles')

@endsection

@section('content')

<div class="Polaris-Page__Header Polaris-Page__Header--hasBreadcrumbs Polaris-Page__Header--hasSecondaryActions Polaris-Page__Header--hasSeparator">
  <div class="Polaris-Page__Navigation">
    <nav role="navigation"><a class="Polaris-Breadcrumbs__Breadcrumb" data-polaris-unstyled="true"><span class="Polaris-Breadcrumbs__Icon"><span
            class="Polaris-Icon"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M12 16a.997.997 0 0 1-.707-.293l-5-5a.999.999 0 0 1 0-1.414l5-5a.999.999 0 1 1 1.414 1.414L8.414 10l4.293 4.293A.999.999 0 0 1 12 16"
                fill-rule="evenodd"></path></svg></span></span><span class="Polaris-Breadcrumbs__Content">Home</span></a></nav>
    </div>
  <div class="Polaris-Page__MainContent">
    <div class="Polaris-Page__TitleAndActions">
      <div class="Polaris-Page__Title">
        <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Statistics</h1>
      </div>
      <div class="Polaris-Page__Actions">
        <!-- <div class="Polaris-Page__PrimaryAction">
            <button type="button" class="Polaris-Button Polaris-Button--primary Polaris-Button--disabled"
            disabled=""><span class="Polaris-Button__Content">
              <span>Save</span></span></button></div> -->
        <div class="Polaris-Page__SecondaryActions">
          <div class="Polaris-Page__IndividualActions"><button class="Polaris-Page__Action" type="button"><span class="Polaris-Page__ActionContent"><span
                  class="Polaris-Page__ActionIcon"><span class="Polaris-Icon"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path
                        d="M13.707 10.707a.997.997 0 0 1-1.414 0L11 9.414V17a1 1 0 1 1-2 0V9.414l-1.293 1.293a.999.999 0 1 1-1.414-1.414l3-3a.999.999 0 0 1 1.414 0l3 3a.999.999 0 0 1 0 1.414zM17 2a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1h-3a1 1 0 1 1 0-2h2V4H4v9h2a1 1 0 1 1 0 2H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h14z"
                        fill-rule="evenodd"></path></svg></span></span><span>Exporl Log</span></span></button></div>
        </div>
      </div>
    </div>
    <!-- <div class="Polaris-Page__PrimaryAction"><button type="button" class="Polaris-Button Polaris-Button--primary Polaris-Button--disabled"
        disabled=""><span class="Polaris-Button__Content"><span>Save</span></span></button></div> -->
  </div>
</div>
<div class="Polaris-Page__Content">

  <div class="Polaris-Layout">

  <div class="Polaris-Layout__AnnotatedSection">
    <div class="Polaris-Layout__AnnotationWrapper">
      <div class="Polaris-Layout__AnnotationContent">
        <div class="Polaris-Card">
          <div class="Polaris-Card__Section">
            <div class="Polaris-SettingAction">
              <div class="Polaris-SettingAction__Setting">
              <span class="badge line-item__badge">6</span>
              <span class="Polaris-Badge Polaris-Badge--statusAttention"><span class="Polaris-VisuallyHidden">Attention</span>Unfulfilled</span>  Order runnig out of stock.</div>
              <div class="Polaris-SettingAction__Action"><button type="button" class="Polaris-Button Polaris-Button--primary"><span
                    class="Polaris-Button__Content"><span>View Orders</span></span></button></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="Polaris-Layout__AnnotatedSection">
    <div class="Polaris-Layout__AnnotationWrapper">
      <div class="Polaris-Layout__AnnotationContent">
        <div class="Polaris-Card">
          <div class="Polaris-Card__Section">
            <div class="Polaris-SettingAction">
              <div class="Polaris-SettingAction__Setting">
              <span class="badge line-item__badge">18</span>
              <span class="Polaris-Badge Polaris-Badge--statusSuccess"><span class="Polaris-VisuallyHidden">Success</span>Rady for pick</span>  Order Tags ​ ready-for-pick​.</div>
              <div class="Polaris-SettingAction__Action"><button type="button" class="Polaris-Button Polaris-Button--primary"><span
                    class="Polaris-Button__Content"><span>View Orders</span></span></button></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <div class="Polaris-Layout__AnnotatedSection">
      <div class="Polaris-Layout__AnnotationWrapper">
        <div class="Polaris-Layout__Annotation">
          <div class="Polaris-TextContainer">
            <h2 class="Polaris-Heading">Style</h2>
            <p>Customize the style of your checkout</p>
          </div>
        </div>
        <div class="Polaris-Layout__AnnotationContent">
          <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
              <div class="Polaris-SettingAction">
                <div class="Polaris-SettingAction__Setting">Upload your store’s logo, change colors and fonts, and more.</div>
                <div class="Polaris-SettingAction__Action"><button type="button" class="Polaris-Button Polaris-Button--primary"><span
                      class="Polaris-Button__Content"><span>Customize Checkout</span></span></button></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="Polaris-Layout__AnnotatedSection">
      <div class="Polaris-Layout__AnnotationWrapper">
        <div class="Polaris-Layout__Annotation">
          <div class="Polaris-TextContainer">
            <h2 class="Polaris-Heading">Account</h2>
            <p>Connect your account to your Shopify store.</p>
          </div>
        </div>
        <div class="Polaris-Layout__AnnotationContent">
          <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
              <div class="Polaris-SettingAction">
                <div class="Polaris-SettingAction__Setting">
                  <div class="Polaris-Stack">
                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                      <div class="Polaris-AccountConnection__Content">
                        <div><span class="Polaris-TextStyle--variationSubdued">No account connected</span></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="Polaris-SettingAction__Action"><button type="button" class="Polaris-Button Polaris-Button--primary"><span
                      class="Polaris-Button__Content"><span>Connect</span></span></button></div>
              </div>
              <div class="Polaris-AccountConnection__TermsOfService">
                <p>
                  By clicking Connect, you are accepting Sample’s <a class="Polaris-Link" href="https://polaris.shopify.com"
                    data-polaris-unstyled="true">Terms and Conditions</a>, including a commission rate of 15% on sales.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="Polaris-Layout__AnnotatedSection">
      <div class="Polaris-Layout__AnnotationWrapper">
        <div class="Polaris-Layout__Annotation">
          <div class="Polaris-TextContainer">
            <h2 class="Polaris-Heading">Form</h2>
            <p>A sample form using Polaris components.</p>
          </div>
        </div>
        <div class="Polaris-Layout__AnnotationContent">
          <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
              <div class="Polaris-FormLayout">
                <div role="group" class="">
                  <div class="Polaris-FormLayout__Items">
                    <div class="Polaris-FormLayout__Item">
                      <div class="">
                        <div class="Polaris-Labelled__LabelWrapper">
                          <div class="Polaris-Label"><label id="TextField1Label" for="TextField1" class="Polaris-Label__Text">First
                              Name</label></div>
                        </div>
                        <div class="Polaris-TextField">
                          <input id="TextField1" value="" placeholder="Tom" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                          <div class="Polaris-TextField__Backdrop"></div>
                        </div>
                      </div>
                    </div>
                    <div class="Polaris-FormLayout__Item">
                      <div class="">
                        <div class="Polaris-Labelled__LabelWrapper">
                          <div class="Polaris-Label"><label id="TextField2Label" for="TextField2" class="Polaris-Label__Text">Last
                              Name</label></div>
                        </div>
                        <div class="Polaris-TextField">
                          <input id="TextField2" value="" placeholder="Ford" class="Polaris-TextField__Input" aria-labelledby="TextField2Label" aria-invalid="false">
                          <div class="Polaris-TextField__Backdrop"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="Polaris-FormLayout__Item">
                  <div class="">
                    <div class="Polaris-Labelled__LabelWrapper">
                      <div class="Polaris-Label"><label id="TextField3Label" for="TextField3" class="Polaris-Label__Text">Email</label></div>
                    </div>
                    <div class="Polaris-TextField">
                      <input id="TextField3" value="" placeholder="example@email.com" class="Polaris-TextField__Input" aria-labelledby="TextField3Label"
                        aria-invalid="false">
                      <div class="Polaris-TextField__Backdrop"></div>
                    </div>
                  </div>
                </div>
                <div class="Polaris-FormLayout__Item">
                  <div class="">
                    <div class="Polaris-Labelled__LabelWrapper">
                      <div class="Polaris-Label"><label id="TextField5Label" for="TextField5" class="Polaris-Label__Text">How
                          did you hear about us?</label></div>
                    </div>
                    <div class="Polaris-TextField Polaris-TextField--multiline">
                      <textarea id="TextField5" placeholder="Website, ads, email, etc." class="Polaris-TextField__Input" aria-labelledby="TextField5Label"
                        aria-invalid="false"></textarea>
                      <div class="Polaris-TextField__Backdrop"></div>
                      <div aria-hidden="true" class="Polaris-TextField__Resizer">
                        <div class="Polaris-TextField__DummyInput">Website, ads, email, etc.<br></div>
                        <div class="Polaris-TextField__DummyInput"><br></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="Polaris-FormLayout__Item">
                  <fieldset class="Polaris-ChoiceList">
                    <ul class="Polaris-ChoiceList__Choices">
                      <li>
                        <label class="Polaris-Choice" for="Checkbox1">
                          <div class="Polaris-Choice__Control">
                            <div class="Polaris-Checkbox">
                              <input type="checkbox" id="Checkbox1" name="ChoiceList1[]" value="false" class="Polaris-Checkbox__Input" aria-invalid="false">
                              <div class="Polaris-Checkbox__Backdrop"></div>
                              <div class="Polaris-Checkbox__Icon">
                                <span class="Polaris-Icon">
                                  <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                    <g fill-rule="evenodd">
                                      <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                      <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                    </g>
                                  </svg>
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="Polaris-Choice__Label">I accept the Terms of Service</div>
                        </label>
                      </li>
                      <li>
                        <label class="Polaris-Choice" for="Checkbox2">
                          <div class="Polaris-Choice__Control">
                            <div class="Polaris-Checkbox">
                              <input type="checkbox" id="Checkbox2" name="ChoiceList1[]" value="false2" class="Polaris-Checkbox__Input" aria-invalid="false">
                              <div class="Polaris-Checkbox__Backdrop"></div>
                              <div class="Polaris-Checkbox__Icon">
                                <span class="Polaris-Icon">
                                  <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                    <g fill-rule="evenodd">
                                      <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                      <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                    </g>
                                  </svg>
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="Polaris-Choice__Label">I consent to receiving emails</div>
                        </label>
                      </li>
                    </ul>
                  </fieldset>
                </div>
                <div class="Polaris-FormLayout__Item"><button type="button" class="Polaris-Button Polaris-Button--primary"><span
                      class="Polaris-Button__Content"><span>Submit</span></span></button></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="Polaris-Layout__Section">
      <div class="Polaris-FooterHelp">
        <div class="Polaris-FooterHelp__Content">
          <div class="Polaris-FooterHelp__Icon">
            <span class="Polaris-Icon Polaris-Icon--colorTeal Polaris-Icon--hasBackdrop">
              <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                <g fill-rule="evenodd">
                  <path d="M6 4.038a2 2 0 1 0-3.999-.001A2 2 0 0 0 6 4.038zm2 0c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4zM18 4a2 2 0 1 0-3.999-.001A2 2 0 0 0 18 4zm2 0c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4zm-2 12a2 2 0 1 0-3.999-.001A2 2 0 0 0 18 16zm2 0c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4zm-14 .038a2 2 0 1 0-3.999-.001A2 2 0 0 0 6 16.038zm2 0c0 2.21-1.79 4-4 4s-4-1.79-4-4 1.79-4 4-4 4 1.79 4 4z"
                    fill-rule="nonzero"></path>
                  <path d="M18 10.038a8 8 0 1 1-16 0 8 8 0 0 1 16 0zM10 14c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" fill="currentColor"></path>
                  <path d="M17 10.038a7 7 0 1 0-14 0 7 7 0 0 0 14 0zm2 0a9 9 0 1 1-18.001-.001A9 9 0 0 1 19 10.038z" fill-rule="nonzero"></path>
                  <path d="M13 10.038a3 3 0 1 0-6 0 3 3 0 0 0 6 0zm2 0c0 2.76-2.24 5-5 5s-5-2.24-5-5 2.24-5 5-5 5 2.24 5 5z" fill-rule="nonzero"></path>
                  <path d="M13.707 7.707l2-2a1 1 0 0 0-1.414-1.414l-2 2a1 1 0 0 0 1.414 1.414zm-1.414 6l2 2a1 1 0 0 0 1.414-1.414l-2-2a1 1 0 0 0-1.414 1.414zM7.707 6.33l-2-2a1 1 0 0 0-1.414 1.415l2 2a1 1 0 0 0 1.414-1.414zm-1.414 6l-2 2a1 1 0 0 0 1.414 1.415l2-2a1 1 0 0 0-1.414-1.414z"
                    fill-rule="nonzero"></path>
                </g>
              </svg>
            </span>
          </div>
          <div class="Polaris-FooterHelp__Text">
            For more details on Polaris, visit our <a class="Polaris-Link" href="https://polaris.shopify.com" data-polaris-unstyled="true">styleguide</a>.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
    @parent


@endsection
