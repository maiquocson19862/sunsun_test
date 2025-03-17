/*----insert ms_kubun--------*/
INSERT INTO `ms_kubun` (`ms_kubun_id`, `kubun_type`, `kubun_id`, `kubun_value`, `sort_no`, `notes`, `time_holiday`) VALUES (185, '032', '1', 'カード', 1, 'クレジットカード', NULL);
INSERT INTO `ms_kubun` (`ms_kubun_id`, `kubun_type`, `kubun_id`, `kubun_value`, `sort_no`, `notes`, `time_holiday`) VALUES (186, '032', '2', '現金', 2, '現地現金', NULL);
INSERT INTO `ms_kubun` (`ms_kubun_id`, `kubun_type`, `kubun_id`, `kubun_value`, `sort_no`, `notes`, `time_holiday`) VALUES (187, '032', '3', '回数券', 3, '回数券', NULL);

/*----backup ms_holiday_acom,ms_setting,tr_notes--------*/
CREATE TABLE ms_holiday_acom_bk AS SELECT * from ms_holiday_acom;
CREATE TABLE ms_setting_bk AS SELECT * from ms_setting;
CREATE TABLE tr_notes_bk AS SELECT * from tr_notes;

DROP TABLE ms_holiday_acom;
DROP TABLE ms_setting;
DROP TABLE tr_notes;

/*----Run code in cmd create table tr_payments_history--------*/
/* php artisan migrate */

/*----restore ms_holiday_acom,ms_setting,tr_notes--------*/
insert into ms_holiday_acom select * from ms_holiday_acom_bk;
insert into ms_setting select * from ms_setting_bk;
insert into tr_notes select * from tr_notes_bk;

DROP TABLE ms_holiday_acom_bk;
DROP TABLE ms_setting_bk;
DROP TABLE tr_notes_bk;

/*----create index tr_yoyaku-----*/
ALTER TABLE tr_yoyaku ADD INDEX tr_yoyaku_1 (booking_id);
ALTER TABLE tr_yoyaku ADD INDEX tr_yoyaku_2 (service_date_start);
ALTER TABLE tr_yoyaku ADD INDEX tr_yoyaku_3 (service_date_end);
ALTER TABLE tr_yoyaku ADD INDEX tr_yoyaku_4 (service_date_start,service_date_end);