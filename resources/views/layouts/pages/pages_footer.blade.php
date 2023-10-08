      <!--footer start here-->
      <footer class="site-footer">
          <div class="container">
              <div class="footer-row">
                  <div class="footer-col footer-link">
                      <div class="footer-widget">
                          <div class="footer-logo">
                              <img src="{{ Storage::url(setting('app_logo')) ? Storage::url('appLogo/app-logo.png') : asset('assets/images/app-logo.png') }}"
                                  alt="footer-logo" class="footer-light-logo">
                              <img src="{{ Utility::getsettings('app_dark_logo') ? Storage::url('appLogo/app-dark-logo.png') : asset('assets/images/app-dark-logo.png') }}"
                                  alt="footer-logo" class="footer-dark-logo">
                          </div>
                          <p>{{ Utility::getsettings('footer_description')
                              ? Utility::getsettings('footer_description')
                              : 'A feature is a unique quality or characteristic that something has. Real-life examples: Elaborately colored tail feathers are peacocks most well-known feature.' }}
                          </p>
                      </div>
                  </div>
                  @if (!empty($footer_main_menus))
                      @foreach ($footer_main_menus as $footer_main_menu)
                          <div class="footer-col">
                              <div class="footer-widget">
                                  <h3>{{ $footer_main_menu->menu }}</h3>
                                  @php
                                      $sub_menus = App\Models\FooterSetting::where('parent_id', $footer_main_menu->id)->get();
                                  @endphp
                                  <ul>
                                      @foreach ($sub_menus as $sub_menu)
                                          @php
                                              $page = App\Models\PageSetting::find($sub_menu->page_id);
                                          @endphp
                                          <li>
                                              <a @if ($page->type == 'link') ?  href="{{ $page->page_url }}"  @else  href="{{ route('description.page', $sub_menu->slug) }}" @endif
                                                  tabindex="0">{{ $page->title }}
                                              </a>
                                          </li>
                                      @endforeach
                                  </ul>
                              </div>
                          </div>
                      @endforeach

                  @endif
              </div>
              <div class="footer-bottom">
                  <div class="row align-items-center">
                      <div class="col-12">
                          <p>© {{ date('Y') }} {{ config('app.name') }}.</p>
                      </div>
                  </div>
              </div>
          </div>
      </footer>
