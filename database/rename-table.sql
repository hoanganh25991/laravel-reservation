DROP TABLE `failed_jobs`;
DROP TABLE `jobs`;
DROP TABLE `migrations`;
RENAME TABLE `outlet_reservation_setting` TO `res_outlet_reservation_setting`;
RENAME TABLE `session` TO `res_session`;
RENAME TABLE `timing` TO `res_timing`;
RENAME TABLE `outlet_reservation_user` TO `res_outlet_reservation_user`;
RENAME TABLE `reservation` TO `res_reservation`;
DROP TABLE `outlet_reservation_user_reset_password`;