@extends('sunsun.front.template')
@section('head')
    @parent
    <style>
        @media only screen and (min-width: 768px) {
            .mean-bar {
                display: none;
            }
        }
        .breadc, .main-body-head {
            display: none;
        }
        .btn:hover {
            opacity: 0.7;
            color: #ffffff;
        }
        a:hover {
            text-decoration: none;
        }

        .warp-button {
            width: 30vw;
            max-width: 100%;
            margin: 0 auto;
        }
        .booking-button-link, .user-button-link, .admin-button-link {
            width: 100%;
            text-align: center;
            padding-bottom: 0.5vw;
        }
        .warp-button .booking-button-link .btn-booking-link {
            background: #d7751e;
            color:#FFFFFF;
            font-size: 2.3vw;
            height: 8vw;
            width: 100%;
        }
        .user-button-link {
            display: flex;
        }
        .user-button-link a {
            flex: 1;
            margin-right: 2px;
        }
        .user-button-link a:first-child {
            margin-right: 3px;
        }
        .btn-user-link {
            background: #d7751e;
            color: #FFFFFF;
        }
        .btn-user-link {
            width: 100%;
            font-size: 1.5vw;
        }

        .btn-admin-link {
            background: rgb(85, 38, 18);
            width: 100%;
            color: #FFFFFF;
            font-size: 1.5vw;
        }
        .main-footer{
            clear: both;
            position: fixed;
            width: 100%;
            bottom: 0px;
            background-color: #faf0f0;
        }
        @media (max-width: 768px) {
            .warp-button {
                width: 67vw;
                font-size: 2.5vw;
            }
            .btn-admin-link, .btn-user-link  {
                font-size: 2.5vw;
            }
            .warp-button .booking-button-link .btn-booking-link {
                height: 17vw;
                font-size: 4.3vw;
            }
        }
        @media (max-width: 768px) {

            .btn-admin-link, .btn-user-link  {
                font-size: 3.5vw;
            }
            .warp-button .booking-button-link .btn-booking-link {
                font-size: 5.3vw;
            }
        }
    </style>
@endsection
@section('page_title', '予約確認')
@section('main')
    <main>
        <div class="container">
            <div class="row  d-flex justify-content-center">
                <div class="col-11">
                    <div class="warp-button">
                        <div class="booking-button-link">
                            <a target="_blank"  href="{{route('home')}}">
                                <button class="btn btn-booking-link">Booking</button>
                            </a>
                        </div>
                        <div class="user-button-link">
                            <a target="_blank"  href="{{route('login')}}">
                                <button class="btn btn-user-link">Login</button>
                            </a>
                            <a target="_blank"  href="{{route('register')}}">
                                <button class="btn btn-user-link">Register</button>
                            </a>
                        </div>
                        <div class="admin-button-link">
                            <a target="_blank" href="{{route('admin-login')}}">
                                <button class="btn btn-admin-link">Admin Login</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    @parent

@endsection
