<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ZooController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingValueController;
use App\Http\Controllers\CoingateController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CommentsReplyController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormValueController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MailTempleteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\FormCommentsController;
use App\Http\Controllers\FormCommentsReplyController;
use App\Http\Controllers\DashboardWidgetController;
use App\Http\Controllers\DocumentGenratorController;
use App\Http\Controllers\DocumentMenuController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MercadoController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MollieController;
use App\Http\Controllers\NotificationsSettingController;
use App\Http\Controllers\PaytmController;
use App\Http\Controllers\PayUMoneyController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\PageSettingController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);
Route::group(['middleware' =>  ['auth', 'verified', 'xss', 'verified_phone']], function () {
    Route::resource('mailtemplate', MailTempleteController::class);
});
Auth::routes();


// 'verified_phone'  middleware
Route::group(['middleware' => ['auth', 'xss', 'Setting', 'verified', '2fa', 'verified_phone']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('profile', ProfileController::class);
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('permission', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::post('/role-permission/{id}', [RoleController::class, 'assignPermission'])->name('roles_permit');
    Route::resource('faqs', FaqController::class);
    Route::resource('module', ModuleController::class);
    Route::resource('poll', PollController::class);
    Route::resource('formvalues', FormValueController::class);
    Route::resource('forms', FormController::class)->except(['show']);

    Route::resource('blogs', BlogController::class)->except(['show']);
    Route::resource('zoos', ZooController::class)->except(['show']);
    Route::resource('blogcategory', BlogCategoryController::class);
    Route::post('blogcategory-status/{id}', [BlogCategoryController::class, 'blogcategorystatus'])->name('blogcategory.status');

    //Booking
    Route::resource('bookings', BookingController::class);
    Route::get('bookings/design/{id}', [BookingController::class, 'design'])->name('booking.design');
    Route::put('bookings/design/update/{id}', [BookingController::class, 'designUpdate'])->name('booking.design.update');
    Route::get('bookings/slots/setting/{id}', [BookingController::class, 'slotsSetting'])->name('booking.slots.setting');
    Route::post('bookings/slots/setting/update/{id}', [BookingController::class, 'slotsSettingupdate'])->name('booking.slots.setting.update');
    Route::get('bookings/slots/time/appoinment/{id}', [BookingController::class, 'appoinmentTime'])->name('booking.appoinment.time');
    Route::get('bookings/slots/seat/appoinment/{id}', [BookingController::class, 'appoinmentSeat'])->name('booking.appoinment.seat');
    Route::get('/bookings/payment/integration/{id}', [BookingController::class, 'bookingpaymentIntegration'])->name('booking.payment.integration');
    Route::post('/bookings/payment/integration/store/{id}', [BookingController::class, 'bookingpaymentIntegrationstore'])->name('booking.payment.integration.store');
    Route::get('calendar/bookings', [BookingController::class, 'bookingCalendar'])->name('booking.calendar');


    //booking value
    Route::get('/booking-values/{id}/view', [BookingValueController::class, 'showBookingsForms'])->name('view.booking.values');
    Route::resource('bookingvalues', BookingValueController::class);
    Route::get('/booking-values/time/{id}/view', [BookingValueController::class, 'timingBookingvaluesShow'])->name('timing.bookingvalues.show');
    Route::get('/booking-values/seats/{id}/view', [BookingValueController::class, 'seatsBookingvaluesShow'])->name('seats.bookingvalues.show');
    Route::get('/booking-values/{id}/time-booking/edit', [BookingValueController::class, 'timeBookingEdit'])->name('booking.time-bookings.edit');
    Route::get('/booking-values/{id}/seats-booking/edit', [BookingValueController::class, 'seatsBookingEdit'])->name('booking.seats-bookings.edit');

    Route::get('/forms/themes/{id}', [FormController::class, 'formTheme'])->name('form.theme');
    Route::get('/forms/themes/edit/{theme}/{id}', [FormController::class, 'formThemeedit'])->name('form.theme.edit');
    Route::post('/forms/themes/update/{id}', [FormController::class, 'formThemeupdate'])->name('form.theme.update');
    Route::get('/forms/integration/{id}', [FormController::class, 'formIntegration'])->name('form.integration');
    Route::get('/forms/payment/integration/{id}', [FormController::class, 'formpaymentIntegration'])->name('form.payment.integration');
    Route::post('/forms/payment/integration/store/{id}', [FormController::class, 'formpaymentIntegrationstore'])->name('form.payment.integration.store');
    Route::post('/forms/integration/{id}', [FormController::class, 'formIntegrationStore'])->name('form.integration.store');
    Route::post('/forms/themes/change/{id}', [FormController::class, 'themeChange'])->name('form.theme.change');

    Route::get('/forms/slack/integration/{id}', [FormController::class, 'slackIntegration'])->name('slack.integration');
    Route::get('/forms/telegram/integration/{id}', [FormController::class, 'telegramIntegration'])->name('telegram.integration');
    Route::get('/forms/mailgun/integration/{id}', [FormController::class, 'mailgunIntegration'])->name('mailgun.integration');
    Route::get('/forms/bulkgate/integration/{id}', [FormController::class, 'bulkgateIntegration'])->name('bulkgate.integration');
    Route::get('/forms/nexmo/integration/{id}', [FormController::class, 'nexmoIntegration'])->name('nexmo.integration');
    Route::get('/forms/fast2sms/integration/{id}', [FormController::class, 'fast2smsIntegration'])->name('fast2sms.integration');
    Route::get('/forms/vonage/integration/{id}', [FormController::class, 'vonageIntegration'])->name('vonage.integration');
    Route::get('/forms/sendgrid/integration/{id}', [FormController::class, 'sendgridIntegration'])->name('sendgrid.integration');
    Route::get('/forms/twilio/integration/{id}', [FormController::class, 'twilioIntegration'])->name('twilio.integration');
    Route::get('/forms/textlocal/integration/{id}', [FormController::class, 'textlocalIntegration'])->name('textlocal.integration');
    Route::get('/forms/messente/integration/{id}', [FormController::class, 'messenteIntegration'])->name('messente.integration');
    Route::get('/forms/smsgateway/integration/{id}', [FormController::class, 'smsgatewayIntegration'])->name('smsgateway.integration');
    Route::get('/forms/clicktell/integration/{id}', [FormController::class, 'clicktellIntegration'])->name('clicktell.integration');
    Route::get('/forms/clockwork/integration/{id}', [FormController::class, 'clockworkIntegration'])->name('clockwork.integration');

    Route::get('/forms/grid/{id?}', [FormController::class, 'grid_view'])->name('grid.form.view');
    Route::post('form/status/{id}', [FormController::class, 'formStatus'])->name('form.status');
    Route::get('/new', [FormController::class, 'NewDemo'])->name('new.sellfie');
    Route::post('/store/demo', [FormController::class, 'WebcamCapture'])->name('webcam.capture');

    // Dashboard-Widget
    Route::get('index-dashboard', [HomeController::class, 'indexdashboard'])->name('index.dashboard');
    Route::get('create-dashboard', [HomeController::class, 'createdashboard'])->name('create.dashboard');
    Route::post('store-dashboard', [HomeController::class, 'storedashboard'])->name('store.dashboard');
    Route::get('edit-dashboard/{id}/edit', [HomeController::class, 'editdashboard'])->name('edit.dashboard');
    Route::put('update-dashboard/{id}', [HomeController::class, 'updatedashboard'])->name('update.dashboard');
    Route::delete('delete-dashboard/{id}', [HomeController::class, 'deletedashboard'])->name('delete.dashboard');
    Route::post('/updatedash/dashboard', [HomeController::class, 'updatedash'])->name('updatedash.dashboard');
    Route::post('/widget/chnages', [HomeController::class, 'WidgetChnages'])->name('widget.chnages');
    Route::post('/widget/chartdata', [DashboardWidgetController::class, 'WidgetChartData'])->name('widget.chartdata');
    Route::post('update-avatar/{id}', [ProfileController::class, 'updateAvatar'])->name('update-avatar');

    Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');
    Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language');
    Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data');
    Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language');
    Route::post('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language');
    Route::delete('/lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy');


    Route::get('/leads/order', [HomeController::class, 'leadsorder'])->name('leads.order');
    Route::post('/change/theme/mode', [HomeController::class, 'changeThememode'])->name('change.theme.mode');
    Route::post('/chart', [HomeController::class, 'formchart'])->name('get.chart.data')->middleware(['auth', 'Setting', 'xss']);
    Route::post('read/notification', [HomeController::class, 'read_notification'])->name('read.notification');

    // user stauts & grid
    Route::post('/user/status/{id}', [UserController::class, 'userStatus'])->name('users.status');
    Route::get('/users/grid/{id?}', [UserController::class, 'grid_view'])->name('grid.view');

    // zoo stauts & grid
    Route::post('/zoos/status/{id}', [ZooController::class, 'zooStatus'])->name('zoos.status');


    //profile update
    Route::post('update-profile-login/{id}', [ProfileController::class, 'updateLogin'])->name('update-login');
    Route::get('account-status/{id}', [UserController::class, 'accountStatus'])->name('account.status');
    Route::get('users/verified/{id}', [UserController::class, 'useremailverified'])->name('user.verified');
    Route::get('users/phoneverified/{id}', [UserController::class, 'userphoneverified'])->name('user.phoneverified');
    Route::get('profile-status', [ProfileController::class, 'profileStatus'])->name('profile.status');
    Route::post('profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');

    //document
    Route::resource('document', DocumentGenratorController::class);
    Route::get('document/design/{id}', [DocumentGenratorController::class, 'design'])->name('document.design');

    //status drag-drop
    Route::post('/document/designmenu', [DocumentGenratorController::class, 'updateDesign'])->name('updatedesign.document');
    Route::get('document-status/{id}', [DocumentGenratorController::class, 'documentStatus'])->name('document.status');

    // menu
    Route::get('docmenu/index', [DocumentMenuController::class, 'index'])->name('docmenu.index');
    Route::get('docmenu/create/{docmenu_id}', [DocumentMenuController::class, 'create'])->name('docmenu.create');
    Route::post('docmenu/store', [DocumentMenuController::class, 'store'])->name('docmenu.store');
    Route::delete('/document/menu/{id}', [DocumentMenuController::class, 'destroy'])->name('document.designdelete');

    // submenu
    Route::get('docsubmenu/create/{id}/{docmenu_id}', [DocumentMenuController::class, 'submenuCreate'])->name('docsubmenu.create');
    Route::post('docsubmenu/store', [DocumentMenuController::class, 'submenuStore'])->name('docsubmenu.store');
    Route::get('/document/submenu/{id}', [DocumentMenuController::class, 'submenuDestroy'])->name('document.submenu.designdelete');
    Route::post('settings/sms-setting/update', [SettingsController::class, 'smsSettingUpdate'])->name('settings/sms-setting/update');

    // Event
    Route::resource('event', EventController::class)->except('show');
    Route::post('event/getdata', [EventController::class, 'get_event_data'])->name('event.get.data');

    // Testimonial
    Route::resource('testimonial', TestimonialController::class);
    Route::post('testimonials/status/{id}', [TestimonialController::class, 'status'])->name('testimonial.status');

    //sms-templates
    Route::resource('sms-template', SmsTemplateController::class);

    // backend- side settings
    Route::post('settings/email-setting/update', [SettingsController::class, 'emailSettingUpdate'])->name('settings/email-setting/update');
    Route::post('settings/auth-settings/update', [SettingsController::class, 'authSettingsUpdate'])->name('settings/auth-settings/update');
    Route::post('test-mail', [SettingsController::class, 'testSendMail'])->name('test.send.mail');
    Route::get('setting/{id}', [SettingsController::class, 'loadsetting'])->name('setting');
    Route::post('settings/app-name/update', [SettingsController::class, 'appNameUpdate'])->name('settings/app-name/update');
    Route::post('settings/app-logo/update', [SettingsController::class, 'appLogoUpdate'])->name('settings/app-logo/update');
    Route::post('settings/GoogleCalender/update', [SettingsController::class, 'GoogleCalender'])->name('settings/GoogleCalender/update');
    Route::post('settings/GoogleMap/update', [SettingsController::class, 'GoogleMapUpdate'])->name('settings/GoogleMap/update')->middleware(['auth', 'Setting', 'xss']);
    Route::post('settings/pusher-setting/update', [SettingsController::class, 'pusherSettingUpdate'])->name('settings/pusher-setting/update');
    Route::post('settings/wasabi-setting/update', [SettingsController::class, 'wasabiSettingUpdate'])->name('settings/wasabi-setting/update');
    Route::post('settings/captcha-setting/update', [SettingsController::class, 'captchaSettingUpdate'])->name('settings/captcha-setting/update');
    Route::post('settings/cookie-setting/update', [SettingsController::class, 'cookieSettingUpdate'])->name('settings/cookie-setting/update');
    Route::post('settings/seo-setting/update', [SettingsController::class, 'seoSettingUpdate'])->name('settings/seo-setting/update');

    // fronted side settings
    Route::get('frontend-setting', [SettingsController::class, 'frontendsetting'])->name('frontend.page');
    Route::post('frontend-setting/store', [SettingsController::class, 'frontendsettingstore'])->name('frontend.page.store');
    Route::post('menu-setting/store', [SettingsController::class, 'menusettingstore'])->name('menu.page.store');
    Route::post('price-setting/store', [SettingsController::class, 'pricesettingstore'])->name('price.page.store');
    Route::post('feature-setting/store', [SettingsController::class, 'featuresettingstore'])->name('feature.page.store');
    Route::post('sidefeature-setting/store', [SettingsController::class, 'sidefeaturesettingstore'])->name('sidefeature.page.store');
    Route::post('privacy-setting/store', [SettingsController::class, 'privacysettingstore'])->name('privacy.page.store');
    Route::post('contactus-setting/store', [SettingsController::class, 'contactussettingstore'])->name('contactus.page.store');
    Route::post('termcondition-setting/store', [SettingsController::class, 'termconditionsettingstore'])->name('termcondition.page.store');
    Route::post('faq-setting/store', [SettingsController::class, 'faqsettingstore'])->name('faq.page.store');
    Route::post('testimonial-setting/store', [SettingsController::class, 'testimonialStore'])->name('testimonialfronted.store');
    Route::post('recaptcha-setting/store', [SettingsController::class, 'recaptchasettingstore'])->name('recaptcha.page.store');
    Route::post('login-setting/store', [SettingsController::class, 'loginsettingstore'])->name('login.page.store');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/test-mail', [SettingsController::class, 'testMail'])->name('test.mail');
    Route::post('/notification/status/{id}', [NotificationsSettingController::class, 'changestatus'])->name('notification.status.change');

    // Page Settings
    Route::resource('page-setting', PageSettingController::class);

    //froentend
    Route::get('landingpage-setting', [LandingPageController::class, 'landingpagesetting'])->name('landingpage.setting');
    Route::post('landingpage-setting/app-setting/store', [LandingPageController::class, 'appsettingstore'])->name('landing.app.store');

    Route::get('landingpage-setting/menu-setting', [LandingPageController::class, 'menusetting'])->name('menusetting.index');
    Route::post('landingpage-setting/menu-setting-section1/store', [LandingPageController::class, 'menusettingsection1store'])->name('landing.menusection1.store');
    Route::post('landingpage-setting/menu-setting-section2/store', [LandingPageController::class, 'menusettingsection2store'])->name('landing.menusection2.store');
    Route::post('landingpage-setting/menu-setting-section3/store', [LandingPageController::class, 'menusettingsection3store'])->name('landing.menusection3.store');

    Route::get('landingpage-setting/feature-setting', [LandingPageController::class, 'featuresetting'])->name('landing.feature.index');
    Route::post('landingpage-setting/feature-setting/store', [LandingPageController::class, 'featuresettingstore'])->name('landing.feature.store');
    Route::get('landingpage-setting/feature/create', [LandingPageController::class, 'feature_create'])->name('feature.create');
    Route::post('landingpage-setting/feature/store', [LandingPageController::class, 'feature_store'])->name('feature.store');
    Route::get('landingpage-setting/feature/edit/{key}', [LandingPageController::class, 'feature_edit'])->name('feature.edit');
    Route::post('landingpage-setting/feature/update/{key}', [LandingPageController::class, 'feature_update'])->name('feature.update');
    Route::get('landingpage-setting/feature/delete/{key}', [LandingPageController::class, 'feature_delete'])->name('feature.delete');

    Route::get('landingpage-setting/business-growth-setting', [LandingPageController::class, 'businessgrowthsetting'])->name('landing.business.growth.index');
    Route::post('landingpage-setting/business-growth-setting/store', [LandingPageController::class, 'businessgrowthsettingstore'])->name('landing.business.growth.store');
    Route::get('landingpage-setting/business-growth/create', [LandingPageController::class, 'business_growth_create'])->name('business.growth.create');
    Route::post('landingpage-setting/business-growth/store', [LandingPageController::class, 'business_growth_store'])->name('business.growth.store');
    Route::get('landingpage-setting/business-growth/edit/{key}', [LandingPageController::class, 'business_growth_edit'])->name('business.growth.edit');
    Route::post('landingpage-setting/business-growth/update/{key}', [LandingPageController::class, 'business_growth_update'])->name('business.growth.update');
    Route::get('landingpage-setting/business-growth/delete/{key}', [LandingPageController::class, 'business_growth_delete'])->name('business.growth.delete');

    Route::get('landingpage-setting/business-growth-view/create', [LandingPageController::class, 'business_growth_view_create'])->name('business.growth.view.create');
    Route::post('landingpage-setting/business-growth-view/store', [LandingPageController::class, 'business_growth_view_store'])->name('business.growth.view.store');
    Route::get('landingpage-setting/business-growth-view/edit/{key}', [LandingPageController::class, 'business_growth_view_edit'])->name('business.growth.view.edit');
    Route::post('landingpage-setting/business-growth-view/update/{key}', [LandingPageController::class, 'business_growth_view_update'])->name('business.growth.view.update');
    Route::get('landingpage-setting/business-growth-view/delete/{key}', [LandingPageController::class, 'business_growth_view_delete'])->name('business.growth.view.delete');

    Route::get('landingpage-setting/start-view-setting', [LandingPageController::class, 'startviewsetting'])->name('landing.start.view.index');
    Route::post('landingpage-setting/start-view-setting/store', [LandingPageController::class, 'startviewsettingstore'])->name('landing.start.view.store');

    Route::get('landingpage-setting/faq-setting', [LandingPageController::class, 'faqsetting'])->name('landing.faq.index');
    Route::post('landingpage-setting/faq-setting/store', [LandingPageController::class, 'faqsettingstore'])->name('landing.faq.store');

    Route::get('landingpage-setting/contactus-setting', [LandingPageController::class, 'contactussetting'])->name('landing.contactus.index');
    Route::post('landingpage-setting/contactus-setting/store', [LandingPageController::class, 'contactussettingstore'])->name('landing.contactus.store');

    Route::get('landingpage-setting/form-setting', [LandingPageController::class, 'formsetting'])->name('landing.form.index');
    Route::post('landingpage-setting/form-setting/store', [LandingPageController::class, 'formsettingstore'])->name('landing.form.store');

    Route::get('landingpage-setting/blog-setting', [LandingPageController::class, 'blogsetting'])->name('landing.blog.index');
    Route::post('landingpage-setting/blog-setting/store', [LandingPageController::class, 'blogsettingstore'])->name('landing.blog.store');

    Route::get('landingpage-setting/footer-setting', [LandingPageController::class, 'footersetting'])->name('landing.footer.index');
    Route::post('landingpage-setting/footer-setting/store', [LandingPageController::class, 'footersettingstore'])->name('landing.footer.store');

    Route::get('landingpage-setting/login-setting', [LandingPageController::class, 'loginSetting'])->name('landing.login.index');
    Route::post('landingpage-setting/login-setting/store', [LandingPageController::class, 'loginSettingStore'])->name('landing.login.store');

    Route::get('landingpage-setting/captcha-setting', [LandingPageController::class, 'captchaSetting'])->name('landing.captcha.index');
    Route::post('landingpage-setting/captcha/store', [LandingPageController::class, 'captchaSettingStore'])->name('landing.captcha.store');

    // Header Setting
    Route::get('landingpage-setting/header-setting', [LandingPageController::class, 'headerSetting'])->name('landing.header.index');
    Route::get('landingpage-setting/header/menu/create', [LandingPageController::class, 'headerMenuCreate'])->name('header.menu.create');
    Route::post('landingpage-setting/header/menu/store', [LandingPageController::class, 'headerMenuStore'])->name('header.menu.store');
    Route::get('landingpage-setting/header/menu/{id}/edit', [LandingPageController::class, 'headerMenuEdit'])->name('header.menu.edit');
    Route::post('landingpage-setting/header/menu/{id}/update', [LandingPageController::class, 'headerMenuUpdate'])->name('header.menu.update');
    Route::get('landingpage-setting/header/menu/{id}/delete', [LandingPageController::class, 'headerMenuDelete'])->name('header.menu.delete');

    //Footer settings
    //Main Menu
    Route::get('landingpage-setting/main/menu/create', [LandingPageController::class, 'footer_main_menu_create'])->name('footer.main.menu.create');
    Route::post('landingpage-setting/main/menu/store', [LandingPageController::class, 'footer_main_menu_store'])->name('footer.main.menu.store');
    Route::get('landingpage-setting/main/menu/edit/{id}', [LandingPageController::class, 'footer_main_menu_edit'])->name('footer.main.menu.edit');
    Route::post('landingpage-setting/main/menu/update/{id}', [LandingPageController::class, 'footer_main_menu_update'])->name('footer.main.menu.update');
    Route::get('landingpage-setting/main/menu/delete/{id}', [LandingPageController::class, 'footer_main_menu_delete'])->name('footer.main.menu.delete');
    // Sub menu
    Route::get('landingpage-setting/sub/menu/create', [LandingPageController::class, 'footer_sub_menu_create'])->name('footer.sub.menu.create');
    Route::post('landingpage-setting/sub/menu/store', [LandingPageController::class, 'footer_sub_menu_store'])->name('footer.sub.menu.store');
    Route::get('landingpage-setting/sub/menu/edit/{id}', [LandingPageController::class, 'footer_sub_menu_edit'])->name('footer.sub.menu.edit');
    Route::post('landingpage-setting/sub/menu/update/{id}', [LandingPageController::class, 'footer_sub_menu_update'])->name('footer.sub.menu.update');
    Route::get('landingpage-setting/sub/menu/delete/{id}', [LandingPageController::class, 'footer_sub_menu_delete'])->name('footer.sub.menu.delete');

    Route::get('landingpage-setting/page-background-setting', [LandingPageController::class, 'pageBackground'])->name('landing.page.background.index');
    Route::post('landingpage-setting/page-background-setting/store', [LandingPageController::class, 'pageBackgroundstore'])->name('landing.page.background.store');


});



Route::get('/{lang?}', [HomeController::class, 'landingPage'])->name('landingpage');

//  Footer page
Route::get('pages/{slug}/{lang?}', [LandingPageController::class, 'pagesView'])->name('description.page');
Route::get('contact/us/{lang?}', [FrontController::class, 'contactus'])->name('contactus');
Route::get('all/faqs/{lang?}', [FrontController::class, 'faqs'])->name('faqs.pages');

 //Blogs pages
 Route::get('/blogs/{slug}/{lang?}', [BlogController::class, 'view_blog'])->name('view.blog');
 Route::get('/see/blogs/{lang?}', [BlogController::class, 'see_all_blogs'])->name('see.all.blogs');
 Route::post('/search/blogs', [BlogController::class, 'search_blogs'])->name('search.blogs');

// sms verify
Route::group(['middleware' => ['Setting', 'xss']], function () {
    Auth::routes(['verify' => true]);
    Route::get('sms/notice', [SmsController::class, 'smsnoticeindex'])->name('smsindex.noticeverification');
    Route::post('sms/notice', [SmsController::class, 'smsnoticeverify'])->name('sms.noticeverification');
    Route::get('sms/verify', [SmsController::class, 'smsindex'])->name('smsindex.verification');
    Route::post('sms/verify', [SmsController::class, 'smsverify'])->name('sms.verification');
    Route::post('sms/verifyresend', [SmsController::class, 'smsresend'])->name('sms.verification.resend');

    Route::get('/register/{lang?}', [RegisterController::class, 'index'])->name('register');
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::get('/login/{lang?}', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('contact_mail', [FrontController::class, 'contact_mail'])->name('contact.mail');
    Route::get('/view/faq', [FrontController::class, 'faq'])->name('faq');
    Route::get('privacy/policy', [FrontController::class, 'privacypolicy'])->name('privacypolicy');
    Route::get('terms/conditions', [FrontController::class, 'termsandconditions'])->name('termsandconditions');
});

Route::resource('comment', CommentsController::class)->middleware(['xss']);
Route::resource('comment_reply', CommentsReplyController::class)->middleware(['xss']);
Route::resource('form_comment', FormCommentsController::class);
Route::resource('form_comment_reply', FormCommentsReplyController::class);

// Form Builder
Route::get('/forms/fill/{id}', [FormController::class, 'fill'])->name('forms.fill')->middleware(['auth', 'Setting', 'xss']);
Route::get('/forms/survey/{id}', [FormController::class, 'publicFill'])->name('forms.survey')->middleware(['xss']);
Route::get('/forms/qr/{id}', [FormController::class, 'qrCode'])->name('forms.survey.qr');
Route::put('/forms/fill/{id}', [FormController::class, 'fillStore'])->name('forms.fill.store')->middleware(['Setting', 'xss']);
Route::get('/form-values/{id}/edit', [FormValueController::class, 'edit'])->name('edit.form.values')->middleware(['auth', 'Setting', 'xss']);
Route::get('/form-values/{id}/view', [FormValueController::class, 'showSubmitedForms'])->name('view.form.values')->middleware(['auth', 'Setting', 'xss']);
Route::post('/form-duplicate', [FormController::class, 'duplicate'])->name('forms.duplicate')->middleware(['auth', 'Setting', 'xss']);
Route::post('ckeditors/upload', [FormController::class, 'ckupload'])->name('ckeditors.upload')->middleware(['auth']);
Route::post('dropzone/upload/{id}', [FormController::class, 'dropzone'])->name('dropzone.upload')->middleware(['Setting']);
Route::post('ckeditor/upload', [FormController::class, 'upload'])->name('ckeditor.upload')->middleware(['auth']);
Route::get('design/{id}', [FormController::class, 'design'])->name('forms.design')->middleware(['auth', 'xss']);
Route::put('/forms/design/{id}', [FormController::class, 'designUpdate'])->name('forms.design.update')->middleware(['auth', 'Setting', 'xss']);

Route::get('/form-values/{id}/download/pdf', [FormValueController::class, 'download_pdf'])->name('download.form.values.pdf')->middleware(['auth', 'Setting', 'xss']);
Route::get('/form-values/{id}/download/csv2', [FormValueController::class, 'download_csv_2'])->name('download.form.values.csv2')->middleware(['auth', 'Setting', 'xss']);
Route::post('/mass/export/xlsx', [FormValueController::class, 'export_xlsx'])->name('mass.export.xlsx')->middleware(['auth', 'Setting', 'xss']);
Route::post('/mass/export/csv', [FormValueController::class, 'export'])->name('mass.export.csv')->middleware(['auth', 'Setting', 'xss']);;
Route::post('filter-chart/{id}', [FormValueController::class, 'getGraphData'])->name('filter_chart')->middleware(['xss']);
Route::post('files/video/store', [FormValueController::class, 'VideoStore'])->name('videostore')->middleware(['xss']);
Route::get('download-image/{id}', [FormValueController::class, 'SelfieDownload'])->name('selfie.image.download')->middleware(['xss']);

Route::get('user/forms/survey/{id}', [HomeController::class, 'userFormQrcode'])->name('users.all.formsSurvey');

//Booking
Route::get('/bookings/survey/time-wise/{id}', [BookingController::class, 'publicTimeFill'])->name('booking.survey.time.wise')->middleware(['xss']);
Route::get('/bookings/survey/seats-wise/{id}', [BookingController::class, 'publicSeatFill'])->name('booking.survey.seats.wise')->middleware(['xss']);
Route::get('/bookings/qr/{id}', [BookingController::class, 'qrCode'])->name('booking.survey.qr');
Route::get('/bookings/appoinment/edit/{id}', [BookingValueController::class, 'editappoinment'])->name('appointment.edit');
Route::delete('/bookings/appoinment/slots-cancel/{id}', [BookingValueController::class, 'SlotCancel'])->name('appointment.slot.cancel');
Route::delete('/bookings/appoinment/seats-cancel/{id}', [BookingValueController::class, 'SeatCancel'])->name('appointment.seat.cancel');
//appoinment time
Route::post('bookings/slots/appoinment/get/{id}', [BookingController::class, 'getappoinmentSlot'])->name('booking.slots.appoinment.get')->middleware(['xss', 'Setting']);
Route::post('bookings/seats/appoinment/get/{id}', [BookingController::class, 'getappoinmentSeat'])->name('booking.seats.appoinment.get')->middleware(['xss', 'Setting']);
Route::put('/bookings/fill/{id}', [BookingController::class, 'fillStore'])->name('booking.fill.store')->middleware(['xss', 'Setting']);



//poll system
Route::group(['middleware' => ['xss']], function () {

    // Poll Management
    Route::get('/poll/fill/{id}', [PollController::class, 'poll'])->name('poll.fill')->middleware(['auth', 'Setting']);
    Route::post('/poll/store/{id}', [PollController::class, 'fillStore'])->name('fill.poll.store');
    Route::post('image/poll/store/{id}', [PollController::class, 'ImageStore'])->name('image.poll.store');
    Route::post('meeting/poll/store/{id}', [PollController::class, 'MeetingStore'])->name('meeting.poll.store');
    Route::get('/poll/image/fill/{id}', [PollController::class, 'ImagePoll'])->name('image.poll.fill')->middleware(['auth']);
    Route::get('/poll/meeting/fill/{id}', [PollController::class, 'MeetingPoll'])->name('meeting.poll.fill')->middleware(['auth']);
    Route::get('/poll/result/{id}', [PollController::class, 'PollResult'])->name('poll.result')->middleware(['auth']);
    Route::get('/poll/image/result/{id}', [PollController::class, 'PollImageResult'])->name('poll.image.result')->middleware(['auth']);
    Route::get('/poll/meeting/result/{id}', [PollController::class, 'PollMeetingResult'])->name('poll.meeting.result')->middleware(['auth']);

    Route::get('/poll/survey/{id}', [PollController::class, 'publicFill'])->name('poll.survey');
    Route::get('/poll/survey/meeting/{id}', [PollController::class, 'PublicFillMeeting'])->name('poll.survey.meeting');
    Route::get('/poll/survey/image/{id}', [PollController::class, 'PublicFillImage'])->name('poll.survey.image');
    Route::get('/poll/share/{id}', [PollController::class, 'Share'])->name('poll.share');
    Route::get('/qr/share/{id}', [PollController::class, 'ShareQr'])->name('poll.share.qr');
    Route::get('/poll/share/image/{id}', [PollController::class, 'ShareImage'])->name('poll.share.image');
    Route::get('/qr/share/image/{id}', [PollController::class, 'ShareQrImage'])->name('poll.share.qr.image');
    Route::get('/poll/share/meeting/{id}', [PollController::class, 'ShareMeeting'])->name('poll.share.meeting');
    Route::get('/qr/share/meeting/{id}', [PollController::class, 'ShareQrMeeting'])->name('poll.share.qr.meeting');
    Route::get('/poll/shares/{id}', [PollController::class, 'Shares'])->name('poll.shares');
    Route::get('/poll/shares/meetings/{id}', [PollController::class, 'ShareMeetings'])->name('poll.shares.meetings');
    Route::get('/poll/shares/images/{id}', [PollController::class, 'ShareImages'])->name('poll.shares.images');
    Route::get('/poll/public/result/{id}', [PollController::class, 'PublicFillResult'])->name('poll.public.result');
    Route::get('/meeting/public/result/{id}', [PollController::class, 'PublicFillResultMeeting'])->name('poll.public.result.meeting');
    Route::get('/image/public/result/{id}', [PollController::class, 'PublicFillResultImage'])->name('poll.public.result.image');
});


// form
// mercado
Route::post('mercado/fill/prepare', [MercadoController::class, 'mercadofillPaymentPrepare'])->name('mercadofillprepare');
Route::get('mercado-fill-payment/{id}', [MercadoController::class, 'mercadofillPlanGetPayment'])->name('mercadofillcallback');

// paytm
Route::post('paytm-payment', [PaytmController::class, 'paytmPayment'])->name('paytm.payment')->middleware(['Setting']);
Route::post('/paytm-callback', [PaytmController::class, 'paytmCallback'])->name('paytm.callback')->middleware(['Setting']);

// coingate
Route::post('coingateprepare', [CoingateController::class, 'coingatePaymentPrepare'])->name('coingateprepare')->middleware(['Setting']);
Route::get('coingate-payment/{id}', [CoingateController::class, 'coingatePlanGetPayment'])->name('coingatecallback')->middleware(['Setting']);

// Form Payumoney
Route::post('payumoney/fill/prepare', [PayUMoneyController::class, 'payumoneyfillPaymentPrepare'])->name('payumoneyfillprepare');
Route::any('payumoney-fill-payment', [PayUMoneyController::class, 'payumoneyfillPlanGetPayment'])->name('payumoneyfillcallback');

// Form Mollie
Route::post('mollie/fill/prepare', [MollieController::class, 'molliefillPaymentPrepare'])->name('molliefillprepare');
Route::any('mollie-fill-payment', [MollieController::class, 'molliefillPlanGetPayment'])->name('molliefillcallback');

//stripe
Route::post('settings/stripe-setting/update', [SettingsController::class, 'paymentSettingUpdate'])->name('settings/stripe-setting/update');
Route::post('settings/social-setting/update', [SettingsController::class, 'socialSettingUpdate'])->name('settings/social-setting/update');


Route::get('/redirect/{provider}', [SocialLoginController::class, 'redirect'])->middleware(['Setting']);
Route::get('/callback/{provider}', [SocialLoginController::class, 'callback'])->name('social.callback')->middleware(['Setting']);

//document
Route::post('/document/design-menu/{id}', [DocumentGenratorController::class, 'documentDesignMenu'])->name('document.design.menu')->middleware('auth', 'verified', '2fa', 'verified_phone');
Route::post('document/status/{id}', [DocumentGenratorController::class, 'DocumentGenStatus'])->name('document.status');

// public document
Route::get('document/public/{slug}', [DocumentGenratorController::class, 'documentPublic'])->name('document.public')->middleware(['xss']);
Route::get('documents/{slug}/{changelog?}', [DocumentGenratorController::class, 'documentPublicMenu'])->name('documentmenu.menu')->middleware(['xss']);
Route::get('document/{slug}/{slugmenu}', [DocumentGenratorController::class, 'documentPublicSubmenu'])->name('documentsubmenu.submenu')->middleware(['xss']);

// extra route ----------------------- notifications
Route::get('form-detail/id', [HomeController::class, 'form_details'])->name('form.details');

// impersonate
Route::impersonate();
Route::get('users/{id}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
Route::get('impersonate/leave', [UserController::class, 'leaveimpersonate'])->name('impersonate.leave');

Route::any('/cookie-consent', [SettingsController::class, 'CookieConsent'])->name('cookie-consent')->middleware(['xss']);

Route::any('/config-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', __('Cache clear successfully.'));
})->name('config.cache')->middleware(['xss']);

Route::get('/invisible', function () {
    return view('invisible');
});
Route::post('/invisible', function (Request $request) {
    $request->validate([
        'g-recaptcha-response' => 'required|captcha'
    ]);
    return 'Data is valid';
});

Route::post('/2fa', function () {
    return redirect(URL()->previous());
})->name('2fa')->middleware('2fa');

Route::group(['prefix' => '2fa'], function () {
    Route::get('/', [LoginSecurityController::class, 'show2faForm']);
    Route::post('/generateSecret', [LoginSecurityController::class, 'generate2faSecret'])->name('generate2faSecret');
    Route::post('/enable2fa', [LoginSecurityController::class, 'enable2fa'])->name('enable2fa');
    Route::post('/disable2fa', [LoginSecurityController::class, 'disable2fa'])->name('disable2fa');
});
