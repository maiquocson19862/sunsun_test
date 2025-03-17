@if($new == 0)
	@foreach($kubun_id as $value)
	<div class="setting-block">
		<div class="setting-laber mt-2">kubun_id</div>
		<div class="setting-content">
			<input type="text" name=""  id="kubun_id" value="{{ $value->kubun_id }}" disabled class="form-control">
		</div>
	</div>
	<div class="setting-block">
		<div class="setting-laber mt-2">kubun_value</div>
		<div class="setting-content">
			<input type="text" name="" id="kubun_value" value="{{ $value->kubun_value }}" class="form-control">
		</div>
	</div>
	<div class="setting-block">
		<div class="setting-laber mt-2">notes</div>
		<div class="setting-content">
			<input type="text" name="" id="notes" value="{{ $value->notes }}" class="form-control">
		</div>
	</div>
	<div class="setting-block">
		<div class="setting-laber"></div>
		<div class="setting-content">
			<div class="setting-validate"></div>
		</div>
	</div>
	<input type="hidden" id="new_check" value='0'>
	@endforeach
@else
	<div class="setting-block">
		<div class="setting-laber mt-2">kubun_id</div>
		<div class="setting-content">
			<input type="text" name="" id="kubun_id" value="" class="form-control">
		</div>
	</div>
	<div class="setting-block">
		<div class="setting-laber mt-2">kubun_value</div>
		<div class="setting-content">
			<input type="text" name="" id="kubun_value" value="" class="form-control">
		</div>
	</div>
	<div class="setting-block">
		<div class="setting-laber mt-2">notes</div>
		<div class="setting-content">
			<input type="text" name="" id="notes" value="" class="form-control">
		</div>
	</div>
	<div class="setting-block">
		<div class="setting-laber"></div>
		<div class="setting-content">
			<div class="setting-validate"></div>
		</div>
	</div>
	<input type="hidden" id="new_check" value='1'>
@endif