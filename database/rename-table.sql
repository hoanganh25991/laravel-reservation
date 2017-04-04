DROP TABLE `failed_jobs`;
DROP TABLE `jobs`;
DROP TABLE `migrations`;
DROP TABLE `outlet_reservation_user_reset_password`;

DROP TABLE `outlet_reservation_setting`;
DROP TABLE `session`;
DROP TABLE `timing`;
DROP TABLE `outlet_reservation_user`;
DROP TABLE `reservation`;

RENAME TABLE `outlet_reservation_setting` TO `res_outlet_reservation_setting`;
RENAME TABLE `session` TO `res_session`;
RENAME TABLE `timing` TO `res_timing`;
RENAME TABLE `outlet_reservation_user` TO `res_outlet_reservation_user`;
RENAME TABLE `reservation` TO `res_reservation`;