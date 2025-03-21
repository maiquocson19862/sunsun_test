@extends('sunsun.front.template')

@section('head')
    @parent
@endsection
@section('page_title', '支払いコールバック')
@section('main')
    <main class="main-body">
        <div class="">
            <form style="display: none" action="/complete" method="POST" id="completeForm">
                @csrf
                <input type="hidden" id="bookingID" name="bookingID" value=">{{ $bookingID }}"/>
                <input type="hidden" id="tranID" name="tranID" value=">{{ $tranID }}"/>
            </form>
        </div>
    </main>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("completeForm").submit();
        });
    </script>
@endsection
