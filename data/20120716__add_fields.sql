ALTER TABLE  `highlight_row` ADD  `cover_id` VARCHAR( 100 ) NULL DEFAULT NULL ,
ADD  `link` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD  `link_label` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD  `description` TEXT NULL DEFAULT NULL ,
ADD  `body` TEXT NULL DEFAULT NULL ,
ADD  `title` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD INDEX (  `cover_id` );


ALTER TABLE  `highlight_row` ADD FOREIGN KEY (  `cover_id` ) REFERENCES  `media_file` (
`id`
);
