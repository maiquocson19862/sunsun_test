<div>
    <input type="hidden" id="new-time" value="{{ isset($new)?$new:'' }}">
    <div class="title-table-time">
        <span class="font-weight-bold">予約時間</span> {{----}}
    </div>
    <table class="table-statistics">
        <thead>
        <tr>
            <th></th>
            @foreach($beds as $bed)
                <th>{{ config('const.laber.bed')[$bed->sort_no] }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($time_room as $key_r => $times)
            <tr>
                <td>
                    <div class="time-col">
                        <span>{{collect($times)->first()->kubun_value}}</span>
                    </div>
                </td>
                @foreach($times as $key_t => $time)
                    <td>
                        <div class="time-col">
                            @if(isset($time->status_time_validate) && $time->status_time_validate == 0)
                                ×
                            @else
                                <div>
                                    <label class="container-radio">
                                        <input type="radio" name="time" value="{{$time->kubun_value}}">
                                        <span class="checkmark"></span>
                                    </label>

                                    <input type="hidden" class="bed" value="{{ $time->kubun_value_room }}">
                                    <input type="hidden" name="data-json" value="{{json_encode($time)}}">
                                </div>
                            @endif
                        </div>
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

