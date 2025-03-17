<?php

namespace App\Exports;

use App\Models\MsUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Controllers\Sunsun\Admin\AdminController;

class UsersExport implements FromCollection,WithHeadings
{
    private $username;
    private $phone;
    private $email;
    private $checkdelete;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(string $username = null, string $phone = null, string $email = null, string $checkdelete = null)
    {
        $this->username = $username;
        $this->phone = $phone;
        $this->email = $email;
        $this->checkdelete = $checkdelete;
    }

    public function collection()
    {
        $result_data = array();
        $search = new AdminController();
        if (!empty($this->username) || !empty($this->phone) || !empty($this->email)) {
            $checkdelete = ($this->checkdelete == 1)? $this->checkdelete : "";
            $arr_data = array(
                'username' => $this->username,
                'phone' => $this->phone,
                'email' => $this->email,
                'notshowdeleted' => $checkdelete
            );

            $_list_data = $search->get_list_search_user($arr_data,1); // type = 1 use export csv
            if (!empty($_list_data)) {
                $data = $_list_data;
            }
        }else{
            if ($this->checkdelete == 1) {
                $data = MsUser::whereNotIn('ms_user_id', [1])->where('deleteflg',0)->get(); // when reload page click download csv
            }else{
                $data = MsUser::whereNotIn('ms_user_id', [1])->get(); // when click search not input after click download csv
            }

        }
        //dd();
        foreach ($data as $row) {
            $user_type = ($row->user_type === "user")?"一般":"アドミン";
            $result_data[] = array(
                '1' => $row->username,
                // '2' => $row->password,
                '2' => $row->tel,
                '3' => $row->email,
                '4' => $row->date_used,
                '5' => $user_type,
                '6' => $row->deleteflg
            );
        }
        return (collect($result_data));
    }

    public function headings(): array
    {
        return [
            '名前',
            // 'パスワード',
            '電話番号',
            'メールアドレス',
            '予約履歴',
            '種類',
            '削除'
        ];
    }
}
