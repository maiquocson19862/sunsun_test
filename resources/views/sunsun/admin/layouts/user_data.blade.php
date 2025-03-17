<table class="result_data_user table table-striped table-hover table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 15%">名前</th>
            <!-- <th scope="col" style="width: 15%">パスワード</th> -->
            <th scope="col" style="width: 15%">電話番号</th>
            <th scope="col" style="width: 20%">メールアドレス</th>
            <th scope="col" style="width: 18%">予約履歴</th>
            <th scope="col" style="width: 10%">種類</th>
            <th scope="col" style="width: 5%">削除</th>
            <th scope="col">編集</th>
        </tr>
    </thead>
    <tbody>
        @if(sizeof($data) > 0)
            <?php $i = 1;?>
            @foreach($data as $items)
                <?php $i++; ?>
                <tr>
                    <td>
                        <span id="username_<?php echo $items->ms_user_id ?>">{{ $items->username }}</span>
                        <div class="required" style="display: none" id="username_<?php echo $items->ms_user_id ?>_user"></div>
                        <input type="text" style="display: none; width: 100%" name="username_<?php echo $items->ms_user_id ?>" id="users_<?php echo $items->ms_user_id ?>" value="{{ $items->username }}" inputmode="katakana">
                    </td>
                    <!-- <td class="col-password">
                        <span id="password_<?php echo $items->ms_user_id ?>">{{ $items->password }}</span>
                        <div class="required" style="display: none" id="password_<?php echo $items->ms_user_id ?>_user"></div>
                        <input type="text" style="display: none; width: 100%" name="password_<?php echo $items->ms_user_id ?>" id="passwords_<?php echo $items->ms_user_id ?>" value="{{ $items->password }}">
                    </td> -->
                    <td>
                        <span id="tel_<?php echo $items->ms_user_id ?>">{{ $items->tel }}</span>
                        <div class="required" style="display: none" id="tel_<?php echo $items->ms_user_id ?>_user"></div>
                        <input type="text" style="display: none; width: 100%" name="tel_<?php echo $i ?>" id="tels_<?php echo $items->ms_user_id ?>" value="{{ $items->tel }}" class="numberphone" maxlength="11">
                    </td>
                    <td>
                        <span id="email_<?php echo $items->ms_user_id ?>">{{ $items->email }}</span>
                        <div class="required" style="display: none" id="email_<?php echo $items->ms_user_id ?>_user"></div>
                        <input type="text" style="display: none; width: 100%" name="email_<?php echo $items->ms_user_id ?>" id="emails_<?php echo $items->ms_user_id ?>" value="{{ $items->email }}">
                    <td><span>{{ $items->date_used }}</span></td>
                    <td style="text-align: center">
                        @if($items->user_type === "user")
                            <span>一般</span>
                        @else
                            <span>アドミン</span>
                        @endif
                    </td>
                    <td style="text-align: center">
                        <input type="checkbox" disabled="disabled" id="users_<?php echo $items->ms_user_id ?>_delete"
                        <?php echo ($items->deleteflg == '1') ? 'checked' : ''; ?> >
                        <input type="checkbox" style="display: none;" name="users_<?php echo $items->ms_user_id ?>_delete" id="users_<?php echo $items->ms_user_id ?>_delete_edit"
                        <?php echo ($items->deleteflg == '1') ? 'checked' : ''; ?> >
                    </td>
                    <td>
                        <div id="<?php echo $items->ms_user_id ?>" class="btn-update" onclick="editSubmit(this.id)" style="display: inline; cursor: pointer">
                            <div class="editbutton">編集</div>
                        </div>
                        <div id="update_<?php echo $items->ms_user_id ?>" onclick="updateSubmit(this.id)" class="btn-update" style="display: none; cursor: pointer">
                            <div class="updatebutton" style="text-align: center; width:48%; margin-right: 4%; float: left; background-color: #d7751e; color:#fff; border-radius:.25rem; font-size:12px">更新</div>
                        </div>
                        <div id="cancel_<?php echo $items->ms_user_id ?>" onclick="cancelSubmit(this.id)" class="btn-update" style="display: none; cursor: pointer">
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
