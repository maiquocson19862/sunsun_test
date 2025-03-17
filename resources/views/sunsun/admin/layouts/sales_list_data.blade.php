<table class="result_data_user table table-striped table-hover table-bordered">
	<thead>
		<tr>
			<th scope="col" style="width: 8%">オーダーID</th>
			<th scope="col" style="width: 7%">オーダー日</th>
			<th scope="col" style="width: 8%">名前</th>
			<th scope="col" style="width: 8%">電話番号</th>
			<th scope="col" >メールアドレス</th>
			<th scope="col" style="width: 5.5%">交通手段</th>
			<th scope="col" style="width: 5%">ご利用</th>
			<th scope="col" style="width: 5%">性別</th>
			<th scope="col" style="width: 4%">年齢</th>
			<th scope="col" style="width: 7%">予約日</th>
			<th scope="col" style="width: 10%">商品名</th>
			<th scope="col" style="width: 5%">単価</th>
			<th scope="col" style="width: 5%">数量</th>
			<th scope="col" style="width: 5%">金額</th>
			<th scope="col" style="width: 5%">支払</th>
		</tr>
	</thead>
	<tbody>
		@if(sizeof($data) > 0)
			<?php $i = 1;?>
			@foreach($data as $items)
				<?php $i++;
				$datetime_cre = new Carbon\Carbon($items->created_at);
				$datetime_cre =  $datetime_cre->format('Y/m/d'); ?>
				<tr>
					<td class="txt_r">
						<span>{{ $items->booking_id }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $datetime_cre }}</span>
					</td>
					<td>
						<span>{{ $items->name }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $items->phone }}</span>
					</td>
					<td>
						<span>{{ $items->email }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $items->transport }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $items->repeat_user }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $items->gender }}</span>
					</td>
					<td class="txt_r">
						<span>{{ $items->age_value }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $items->date_value }}</span>
					</td>
					<td>
						<span>{{ $items->product_name }}</span>
					</td>
					<td class="txt_r">
						<span>{{ $items->price }}</span>
					</td>
					<td class="txt_r">
						<span>{{ $items->quantity }}</span>
					</td>
					<td class="txt_r">
						<span>{{ $items->money }}</span>
					</td>
					<td class="txt_c">
						<span>{{ $items->payment_method }}</span>
					</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>
<div class="pagination {{ ($type==1) ? 'pagination_ajax' : ''}}">
	{{ $data->links('sunsun.admin.layouts.pagination') }}
</div>
