const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')

    .sass('resources/assets/sunsun/common/scss/reset.scss', 'public/common/css')
    .sass('resources/assets/sunsun/front/scss/base.scss', 'public/sunsun/front/css')

    .js('resources/assets/sunsun/front/js/base.js', 'public/sunsun/front/js')
    .js('resources/assets/sunsun/front/js/booking.js', 'public/sunsun/front/js')
    .js('resources/assets/sunsun/front/js/confirm.js', 'public/sunsun/front/js')
    .js('resources/assets/sunsun/front/js/payment.js', 'public/sunsun/front/js')
    .js('resources/assets/sunsun/front/js/add_user_booking.js', 'public/sunsun/front/js')

    .sass('resources/assets/sunsun/front/scss/booking.scss', 'public/sunsun/front/css')
    .sass('resources/assets/sunsun/front/scss/booking-mobile.scss', 'public/sunsun/front/css')

    //auth
    .js('resources/assets/sunsun/auth/js/validate-form.js', 'public/sunsun/auth/js')

    //admin
    .sass('resources/assets/sunsun/admin/scss/day.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/monthly.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/admin.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/weekly.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/setting.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/holiday.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/setting_accommodation_flg.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/css/on-off-switch.scss', 'public/css')
    .sass('resources/assets/sunsun/admin/scss/time_off.scss', 'public/sunsun/admin/css')
    .sass('resources/assets/sunsun/admin/scss/day_off.scss', 'public/sunsun/admin/css')

    .js('resources/assets/sunsun/admin/js/setting.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/admin.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/weekly.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/day.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/month.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/time_off.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/day_off.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/sales_list.js', 'public/sunsun/admin/js')
    .js('resources/assets/sunsun/admin/js/day_on.js', 'public/sunsun/admin/js')
    .copyDirectory('resources/assets/sunsun/imgs', 'public/sunsun/imgs')
    .copyDirectory('resources/assets/sunsun/lib', 'public/sunsun/lib')
    .copyDirectory('resources/assets/sunsun/svg', 'public/sunsun/svg');
