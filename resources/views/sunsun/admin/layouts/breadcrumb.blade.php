<p class="breadc">
    <span class="">
        <a href="/" class="breadc-link">ホーム</a> &#160;&#160;&gt;&#160; @yield('title')
    </span>
    <span class="">
        @if(Auth::check())
            <a class="breadc-link" href="{{ route('.edit') }}">{{ isset(Auth::user()->username) ? Auth::user()->username:'' }}</a>さま&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="breadc-link" href="{{ route('logout') }}">@lang('auth.logout')</a>
        @else
            <a class="breadc-link" href="{{ route('login') }}">@lang('auth.login')</a>
        @endif
    </span>
</p>
<div class="main-body-head text-center mb-3">
    <h1 class="title-menu text-left">@yield('title')</h1>
</div>
