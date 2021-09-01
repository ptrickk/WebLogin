create table visits
(
	visitID int auto_increment
		primary key,
	userID int null,
	time datetime null,
	constraint userID
		foreign key (userID) references user (userID)
);

