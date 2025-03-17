<?php
return [
    'repeat_user' => [
        'label' => 'ご利用',
        'options' => [
            'no' => 'はじめて',
            'yes' => 'リピート',
        ]
    ],
    'transport' => [
        'label' => '交通手段',
        'options' => [
            'car' => '車',
            'bus' => 'バス',
        ]
    ],
    'bus_arrive_time_slide' => [
        'label' => '洲本IC着',
        'options' => [
            '1' => '9:29着（三宮発）',
            '2' => '10:29着（三宮発）',
            '3' => '11:14着（三宮発）',
            '4' => '12:38着（舞子）',
        ]
    ],
    'pick_up' => [
        'label' => '送迎',
        'options' => [
            'yes' => '希望する',
            'no' => '希望しない',
        ]
    ],
    'course' => [
        'label' => 'コース',
        'options' => [
            'normal' => '酵素浴',
            'day' => '1日リフレッシュコース',
            'eat' => '酵素部屋貸切プラン',
            'pet' => 'ペット酵素浴',
            'no' => '断食プラン',
        ]
    ],
    'gender' => [
        'label' => '性別',
        'options' => [
            'male' => '男性',
            'female' => '女性',
        ]
    ],
    'age' => [
        'label' => '年齢',
        'age1' => '小学生',
        'age2' => '学生(中学生以上)',
        'age3' => '大人',
        'options' => [
            '19' => '19',
            '20' => '20',
            '21' => '21',
            '22' => '22',
        ]
        ,'age4' => '中学生以下'
    ],
    'date' => [
        'label' => '予約日',
    ],
    'time' => [
        'label' => '予約時間',
        'laber1' => '入浴1回目',
        'laber2' => '入浴2回目',
    ],
    'lunch' => [
        'label' => 'ランチ',
        'options' => [
            'no' => '無し',
            'yes' => '有り',
        ]
        ,'note' => 'ランチは11:30〜12:00の間にご用意させて頂きます'
        ,'note_confirm' => '<p>※ランチは11:30〜12:00の間にご用意させて頂きます。</p>'
        ,'note_confirm1' => "<label style='color:red'>※ランチのご予約は前日までとなります。</label>"
    ],
    'core_tuning' => [
        'label' => 'コアチューニング'
    ],
    'whitening' => [
        // 2020/05/29 son edit 138
        'label' => 'ニュースキャン',
        'label_time' => 'ニュースキャン時間',
        // 2020/05/29 son edit end
        'options' => [
            'no' => '追加しない',
            'yes' => '追加する',
        ]
        ,'note' => '酵素風呂とは別に30分程度お時間が必要になります。'
    ],
    'whitening2' => [
        'label' => 'ホワイトニング'
    ],
    'pet' => [
        'label' => 'ペット預かり',
        'options' => [
            'no' => '追加しない',
            'yes' => '追加する',
        ]
    ],
    'room' => [
        'label' => '宿泊(部屋ﾀｲﾌﾟ)',
        'options' => [
            'no' => '無し',
            '1' => 'ラックスルーム（～2名）',
            '2' => 'ツイン（１～２名）',
            '3' => 'シングル（1名）',
        ]
    ],
    'stay_guest_num' => [
        'label' => '宿泊人数',
        'options' => [
            '1' => '1名',
            '2' => '2名',
            '3' => '3名',
        ]
    ],
    'range_date' => [
        'label' => '宿泊日',
        'checkin' => 'チェックイン',
        'checkout' => 'チェックアウト',
    ],
    'number_guests_book' => [
        'label' => '人数',
        'options' => [
            '1' => '1名',
            '2' => '2名',
            '3' => '3名',
            '4' => '1名',
            '5' => '2名',
            '6' => '3名',
        ]
    ],
    'number_guests_book' => [
        'label' => '人数',
        'options' => [
            '1' => '1名',
            '2' => '2名',
            '3' => '3名',
            '4' => '4名',
            '5' => '5名',
            '6' => '6名',
        ]
    ],
    'number_lunch_book' => [
        'label' => 'ランチ',
        'options' => [
            'no' => '無し',
            '1' => '1名',
            '2' => '2名',
            '3' => '3名',
            '4' => '4名',
            '5' => '5名',
            '6' => '6名',
        ]
    ],
    'range_date_eat' => [
        'label' => '利用期間',
        'start' => '開始日',
        'end' => '終了日',
    ],
    'range_time_eat' => [
        'label' => '入浴時間',
    ],
    'number_pet' => [
        'label' => 'ペット数',
        'options' => [
            '1' => '1匹',
            '2' => '2匹',
            '3' => '3匹',
        ]
    ],
    'pet_type' => [
        'label' => 'ペット種類',
    ],
    'name' => [
        'label' => '名前',
        'node_label' => '（カタカナ）',
        'placeholder' => '全角カタカナ',
    ],
    'phone' => [
        'label' => '電話番号',
    ],
    'email' => [
        'label' => 'メールアドレス',
    ],
    'services_used' => [
        'label' => 'ご購入金額',
    ],
    'total' => [
        'label' => '合計',
    ],
    'payment_method' => [
        'label' => 'お支払い方法',
    ],
];