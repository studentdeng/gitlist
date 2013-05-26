create table user
(
  id                       INTEGER(32) not null auto_increment,
  username                 VARCHAR(255),
  password                 VARCHAR(255),
  parent_id                INTEGER(32),
  nickname	               VARCHAR(255),
  avatar_url               VARCHAR(255),
  gender                   INTEGER(8),
  platform_userid          INTEGER(8),
  platform_type            INTEGER(8),
  created                  DATETIME,
  PRIMARY KEY (id)
);

create table notice
(
id                       INTEGER(32) not null auto_increment,
user_id                  INTEGER(32),
content                  VARCHAR(255),
rendered	             VARCHAR(255),
created                  DATETIME,
modified                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
source                   INTEGER(8),
location                 VARCHAR(255),
content_type             INTEGER(32),
comments_count           INTEGER(32),
reply_to                 INTEGER(32),
PRIMARY KEY (id)
);

create table reply
(
notice_id                INTEGER(32),
notice_user_id           INTEGER(32),
replied_id               INTEGER(32),
modified	             DATETIME,
content_type             INTEGER(32)
);

create table app
(
  id                       INTEGER(32) not null auto_increment,
  user_id                  INTEGER(32),
  download_url             VARCHAR(255),
  device_type              INTEGER(8),
  display_name	           VARCHAR(255),
  icon_72                  VARCHAR(255),
  icon_small_50            VARCHAR(255),
  icon_small               VARCHAR(255),
  icon                     VARCHAR(255),
  icon_x2                  VARCHAR(255),
  loading_image_x2         VARCHAR(255),
  created                  DATETIME,
  modified                 DATETIME,
  PRIMARY KEY (id)
);

create table file
(
  id                       INTEGER(11) not null auto_increment,
  url                      VARCHAR(255),
  mimetype                 VARCHAR(255),
  size                     INTEGER(32),
  title 	           VARCHAR(255),
  date                     VARCHAR(255),
  link                     VARCHAR(255),
  created                  DATETIME,
  PRIMARY KEY (id)
);

create table file_to_post
(
  file_id                  VARCHAR(255),
  post_id                  VARCHAR(255),
  modified                 DATETIME
);

create table statistics
(
  id                      INTEGER(32) not null auto_increment,
  app_id                  INTEGER(32),
  content                 VARCHAR(255),
  category                INTEGER(32),
  created                 DATETIME,
  PRIMARY KEY (id)
);

