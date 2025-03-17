<?php 
    function departDate($date){
        if(!empty($date)) {
            $year = substr($date,0,4);
            $month = substr($date,4,2);
            $day = substr($date,6,2);
            return  $year.'/'.$month.'/'.$day;
        } 
    }
?>
<table class="result_data_holiday table table-striped table-hover table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 1%">.No</th>
            <th scope="col" style="width: 35%">祝日年月日</th>
            <th scope="col" style="width: 35%">注意</th>
            <th scope="col" style="width: 10%">削除</th>
            <th scope="col">編集</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) > 0)
            <?php $i = 1 ?>
            @foreach($data as $items)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        <span id="holiday_<?php echo $items->ms_holiday_id ?>_date">{{ departDate($items->date_holiday) }}</span>
                        <div class="required" style="display: none" id="holiday_<?php echo $items->ms_holiday_id ?>"></div>
                        <input type="text" style="display: none; width: 100%" name="holiday_<?php echo $items->ms_holiday_id ?>_date" id="holidays_<?php echo $items->ms_holiday_id ?>_date" value="{{ departDate($items->date_holiday) }}">
                    </td>
                    
                    <td>
                        <span id="holiday_<?php echo $items->ms_holiday_id ?>_note">{{ $items->note_holiday }}</span>
                        <div class="required" style="display: none" id="holidays_<?php echo $items->ms_holiday_id ?>_note"></div>
                        <input type="text" style="display: none; width: 100%" name="holiday_<?php echo $items->ms_holiday_id ?>" id="holiday_<?php echo $items->ms_holiday_id ?>_notes" value="{{ $items->note_holiday }}">
                    </td>
                    <td style="text-align: center">
                        <input type="checkbox" disabled="disabled" name="holiday_<?php echo $items->ms_holiday_id ?>_delete" id="holiday_<?php echo $items->ms_holiday_id ?>_delete">
                    </td>
                    <td>
                        <div id="<?php echo $items->ms_holiday_id ?>" class="btn-update" onclick="editSubmit(this.id)" style="display: inline; cursor: pointer">
                            <div class="editbutton">編集</div>
                        </div>
                        <div id="update_<?php echo $items->ms_holiday_id ?>" onclick="updateSubmit(this.id)" class="btn-update" style="display: none; cursor: pointer">
                            <div class="updatebutton" style="text-align: center; width:48%; margin-right: 4%; float: left; background-color: #d7751e; color:#fff; border-radius:.25rem; font-size:12px">更新</div>
                        </div>
                        <div id="cancel_<?php echo $items->ms_holiday_id ?>" onclick="cancelSubmit(this.id)" class="btn-update" style="display: none; cursor: pointer">
                            <div class="cancelbutton" style="text-align: center; width:48%; float: left; background-color: #79836f; color:#fff; border-radius:.25rem; font-size:10px">キャンセル</div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div class="pagination {{ ($type==1) ? 'pagination_ajax' : ''}}">
    {{ $data->links('sunsun.admin.layouts.pagination') }}
</div>