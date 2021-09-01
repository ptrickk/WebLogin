create table user
(
	userID int auto_increment
		primary key,
	username varchar(255) null,
	firstname varchar(255) null,
	surname varchar(255) null,
	email varchar(255) not null,
	birthday date null,
	password varchar(255) not null,
	constraint user_email_uindex
		unique (email),
	constraint user_username_uindex
		unique (username)
);

