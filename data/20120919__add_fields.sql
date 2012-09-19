ALTER TABLE  `highlight_row` CHANGE  `proxy_pk`  `proxy_pk` VARCHAR( 255 ) CHARACTER SET utf8  NULL DEFAULT NULL;
ALTER TABLE  `highlight_container` ADD  `description` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `name`;
