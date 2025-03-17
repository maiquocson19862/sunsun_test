<header class="main-header  mean-container">
    <div class="container d-none d-md-block" id="header">
        <div class="row">
            <div class="col-2 mt-4">
                <h1>
                    <a href="http://sun-sun33.com/">
                        <img src="{{ asset('sunsun/imgs/logo.png').config('version_files.html.js') }}" class="img-fluid" alt="ぬか酸素 Sun燦 淡路島の酵素風呂">
                    </a>
                </h1>
            </div>
            <!-- End of col -->
            <div class="col-10">
                <div class="d-flex justify-content-end text-right">
                    <div><a href="https://sun33.thebase.in/"><img src="{{ asset('sunsun/imgs/btn-shop.png') }}" alt="ネットショップはこちら" class="img-fluid"></a></div>
                    <div><a href="http://sun-sun33.com/archives/category/voice"><img src="{{ asset('sunsun/imgs/btn-voice.png') }}" alt="お客様の声" class="img-fluid"></a></div>
                    <div><a href="http://sun-sun33.com/shop"><img src="{{ asset('sunsun/imgs/btn-access.png') }}" alt="アクセス情報" class="img-fluid"></a></div>
                </div>
                <!-- End of row -->

                <div class="d-flex justify-content-end text-right pb-3 mt-5">
                    <div class="pr-3 pr-lg-5 pb-3 border border-top-0 border-right-0 border-left-0 border-dashed border-brown">
                        <a href="http://sun-sun33.com/">
                            <img src="{{ asset('sunsun/imgs/nav1.png') }}" alt="トップページへ" class="img-fluid">
                        </a>
                    </div>
                    <div class="pr-3 pr-lg-5 pb-3 border border-top-0 border-right-0 border-left-0 border-dashed border-brown">
                        <a href="http://sun-sun33.com/about">
                            <img src="{{ asset('sunsun/imgs/nav2.png') }}" alt="酵素風呂とは？" class="img-fluid">
                        </a>
                    </div>
                    <div class="pr-3 pr-lg-5 pb-3 border border-top-0 border-right-0 border-left-0 border-dashed border-brown">
                        <a href="http://sun-sun33.com/menu">
                            <img src="{{ asset('sunsun/imgs/nav3.png') }}" alt="メニュー・ご利用料金" class="img-fluid">
                        </a>
                    </div>
                    <div class="pr-3 pr-lg-5 pb-3 border border-top-0 border-right-0 border-left-0 border-dashed border-brown">
                        <a href="http://sun-sun33.com/flow">
                            <img src="{{ asset('sunsun/imgs/nav4.png') }}" alt="酵素風呂の入り方" class="img-fluid">
                        </a>
                    </div>
                    <div class="pr-3 pr-lg-5 pb-3 border border-top-0 border-right-0 border-left-0 border-dashed border-brown">
                        <a href="http://sun-sun33.com/shop">
                            <img src="{{ asset('sunsun/imgs/nav5.png') }}" alt="お店のご紹介" class="img-fluid">
                        </a>
                    </div>
                    <div class=" pb-3 border border-top-0 border-right-0 border-left-0 border-dashed border-brown">
                        <a href="http://sun-sun33.com/contact">
                            <img src="{{ asset('sunsun/imgs/nav6.png') }}" alt="お問い合わせ" class="img-fluid">
                        </a>
                    </div>
                </div>
                <!-- End of row -->
            </div>
            <!-- End of col -->
        </div>
        <!-- End of row -->
    </div>
    <div class="mean-bar">
        <a id="nav" class="meanmenu-reveal" data-toggle="collapse" data-target=".nav-menu" style="right: 0px; left: auto; text-align: center; text-indent: 0px; font-size: 18px;">
            <span></span><span></span><span></span>
        </a>
        <nav class="mean-nav">
            <p><a href="https://sun33.thebase.in/" class="text-white">ネットショップ</a> ｜ <a href="http://sun-sun33.com/archives/category/voice" class="text-white">お客様の声</a> ｜ <a href="http://sun-sun33.com/shop" class="text-white">アクセス</a></p>
            <ul class="nav-menu collapse">
                <li><a href="http://sun-sun33.com/">トップページ</a></li>
                <li><a href="http://sun-sun33.com/about">酵素風呂とは?</a></li>
                <li><a href="http://sun-sun33.com/menu">メニュー・ご利用料金</a></li>
                <li><a href="http://sun-sun33.com/flow">酵素風呂の入り方</a></li>
                <li><a href="http://sun-sun33.com/shop">お店のご紹介</a></li>
                <li class="mean-last"><a href="http://sun-sun33.com/contact">お問い合わせ</a></li>
            </ul>
        </nav>
    </div>

    <p class="breadc">
        <span class="">
            <a href="/" class="breadc-link">ホーム</a> &#160;&#160;&gt;&#160; @yield('page_title')
        </span>
        <span class="">
            @if(Auth::check())
                <a class="breadc-link" href="{{ route('.edit') }}">{{ isset(Auth::user()->username) ? Auth::user()->username:'' }}</a>さま<a class="breadc-link" style="margin-left: 1vw" href="{{ route('logout') }}">@lang('auth.logout')</a>
            @else
                <a class="breadc-link" href="{{ route('login') }}">@lang('auth.login')</a>
            @endif
        </span>
    </p>


    <div class="main-body-head text-center mb-3">
        <h1 class="title-menu text-left">@yield('page_title')</h1>
    </div>
</header>
