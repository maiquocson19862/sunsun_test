@extends('sunsun.admin.template')
@section('title', 'Admin')
@section('head')
    @parent
    <style>
        .btn:hover {
            opacity: 0.7;
            color: #ffffff;
        }
        a:hover {
            text-decoration: none;
        }
        .warp-content {
            padding-top: 75px;
        }
        .warp-content {
            width: 380px;
            max-width: 100%;
            margin: 0 auto;

        }

        .day-btn-day {
            padding-bottom: 8px;
        }


        button.btn.btn-link-day {
            width: 100%;
            height: 90px;
            background: #d7751e;
            color:#FFFFFF;
            font-size: 1.5rem;
        }

        .week-month-btn {
            display:flex;
        }

        .week-month-btn a {
            flex: 1;

        }
        .week-month-btn a:first-child {
            margin-right: 5px;
        }
        .week-month-btn button {
            width: 100%;
            color: #FFFFFF;
            background: #d7751e;
            height: 90px;
            font-size: 1.5rem;
        }
    </style>
@endsection
@section('main')
   <main>
       <div class="container">
           <div class="row  d-flex justify-content-center">
               <div class="col-11">
                   <div class="warp-content">
                        <div class="day-btn-day">
                            <a href="{{route('admin.day')}}">
                                <button class="btn btn-link-day">
                                    １日表示
                                </button>
                            </a>
                        </div>
                       <div class="week-month-btn">
                           <a href="{{route('admin.weekly')}}">
                               <button class="btn btn-week">週間表示</button>
                           </a>
                           <a href="{{route('admin.monthly')}}">
                               <button class="btn btn-month">月間表示</button>
                           </a>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </main>

@endsection


@section('footer')
    @parent
@endsection

@section('script')
    @parent
    <script>
        if ($(window).width() >= 768) {
            var height_header = $('header').outerHeight();
            var height_footer = $('footer').outerHeight();
            var height_window = $(window).height();
            $('main').css('min-height', height_window - (height_footer + height_header) +"px" )
        }
    </script>
@endsection

