<div class="modal" id="modal_confirm">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-time">
            <!-- Modal body -->
            <div class="modal-body-time">
                <center>{!! config('const.message.confirm_change_course') !!}</center>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer" style="padding: 10px;">
                <button type="button" class="btn btn-modal-left text-white color-primary" id="confirm_ok" style="padding: 0.375rem 2rem;">
                    選択
                </button>
                <button type="button" class="btn btn-outline-dark  btn-modal-right" style="padding: 0.375rem 1rem;" id="confirm_cancel">
                    閉じる
                </button>
            </div>
        </div>
    </div>
</div>
