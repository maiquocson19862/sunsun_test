{{ isset($booking_data->booking_data['name'])?$booking_data->booking_data['name']:'' }} 様

この度は、【ぬか酸素Sun燦】にご予約いただき、誠にありがとうございます。
ご予約の日程が近づいてまいりましたので、ご予約内容をご確認ください。

＜＜ご予約内容・注意事項＞＞
@php
    //Regex lấy đoạn body của html
    preg_match('|confirm"[^>]*>(.*?)(?=\<div class="foot-confirm")|si', $booking_data->booking_html, $match);
    $string_change_multi = preg_replace('/\s+/', '
', str_replace(' ', '', strip_tags($match[1])));
    $string_change_space = preg_replace('/\s*mark_space\s*/', '  ', $string_change_multi);
    $string_change_newline = preg_replace('/\s*mark_newline\s*/', '

', $string_change_space);
    echo preg_replace('/\s*mark_realline\s*/', '

- - - - - - - - - - - - - - - - - - - - - - -

', $string_change_newline);
@endphp

■アクセス・詳細情報
http://sun-sun33.com/shop
@if((isset($booking_data->check_has_couse_oneday) === true) && ($booking_data->check_has_couse_oneday === true))

■お持ちいただく物
・基礎化粧品、メイク用品、ヘアブラシなど
・季節に合わせてリラックスできるお洋服（リラックスタイムに使用）
@elseif((isset($booking_data->check_has_note) === true) && ($booking_data->check_has_note === true))
■お持ちいただく物
基礎化粧品、メイク用品、ヘアブラシなど
@endif

何かご不明な点等ございましたら、下記お問合せ先までご連絡いただければ幸いです。

どうぞよろしくお願い申し上げます。

-----------------------------------------------------------
ぬか酸素 Sun燦
〒656-0131 兵庫県南あわじ市広田中筋296-1
ホームページ http://sun-sun33.com
メールアドレス：arigatoukouso@sun-sun33.com
TEL：0799-20-7801　FAX：0799-20-7802
