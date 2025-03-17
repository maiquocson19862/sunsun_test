{{ isset($user_data->username)?$user_data->username:'' }} 様

この度は、【ぬか酸素Sun燦】にユーザー登録いただき、誠にありがとうございます。
{{ isset($user_data->username)?$user_data->username:'' }}様のユーザー登録が完了しました。

【ご登録情報】

ユーザー名：{{ isset($user_data->username)?$user_data->username:'' }}
メールアドレス：{{ isset($user_data->email)?$user_data->email:'' }}

※パスワードはセキュリティ保持のため、表示しておりません。
※ご登録情報は　【　アカウント情報　】　のページで確認・変更できます。
{{ isset($app_url)?$app_url . "/profile":'' }}

何かご不明な点等ございましたら、下記お問合せ先までご連絡いただければ
幸いです。

どうぞよろしくお願い申し上げます。

-----------------------------------------------------------
ぬか酸素 Sun燦
〒656-0131 兵庫県南あわじ市広田中筋296-1
ホームページ http://sun-sun33.com
メールアドレス：arigatoukouso@sun-sun33.com
TEL：0799-20-7801　FAX：0799-20-7802
