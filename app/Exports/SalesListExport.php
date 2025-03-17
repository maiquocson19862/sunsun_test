<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Controllers\Sunsun\Admin\AdminController;
use Carbon\Carbon;

class SalesListExport implements FromCollection,WithHeadings
{
	private $data_search;
	/**
	* @return \Illuminate\Support\Collection
	*/

	public function __construct($data_search = null)
	{
		$this->data_search = $data_search;
	}

	public function collection()
	{
		$result_data = array();
		$search = new AdminController();
		$list_data = $search->get_salse_list($this->data_search,1);
		foreach ($list_data as $row) {
			$result_data[] = array(
				'1' => $row->booking_id,
				'2' => (new Carbon($row->created_at))->format('Y/m/d'),
				'3' => $row->name,
				'4' => $row->phone,
				'5' => $row->email,
				'6' => $row->transport,
				'7' => $row->repeat_user,
				'8' => $row->gender,
				'9' => $row->age_value,
				'10' => $row->date_value,
				'11' => $row->product_name,
				'12' => $row->price,
				'13' => $row->quantity,
				'14' => $row->money,
				'15' => $row->payment_method
			);
		}
		return (collect($result_data));
	}

	public function headings(): array
	{
		return [
			'オーダーID',
			'オーダー日',
			'名前',
			'電話番号',
			'メールアドレス',
			'交通手段',
			'ご利用',
			'性別',
			'年齢',
			'予約日',
			'商品名',
			'単価',
			'数量',
			'金額',
			'支払'
		];
	}
}
