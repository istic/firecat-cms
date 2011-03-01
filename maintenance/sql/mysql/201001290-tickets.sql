create table `ticket`(
    id int auto_increment,
    date_created datetime,
    date_modified timestamp,
    date_due datetime,
    date_closed datetime,
    percentage_done tinyint,

    owner_user_id int,
    reporter_user_id int,
    owner_group_id int,
    awaiting_input_user_id int,

    name varchar(255),
    description mediumtext,
    
    priority int default 5,
    status tinyint,

    PRIMARY KEY(id)
);

create table admingroup (
    id int auto_increment,
    groupname varchar(63),

    PRIMARY KEY(id)
);

create table `user_admingroup` (
    id int auto_increment,
    admingroup_id int,
    user_id int,

    PRIMARY KEY(id)
);

insert into admingroup (groupname) values("Ministry for Administration");
insert into admingroup (groupname) values("Department of Non Player Characters");
insert into admingroup (groupname) values("Board of Technomancy");
insert into admingroup (groupname) values("The Project Management Presidium");
insert into admingroup (groupname) values("Narrative Directorate");
insert into admingroup (groupname) values("Department for Logistics");
insert into admingroup (groupname) values("Treasury Department");
insert into admingroup (groupname) values("Niche of Meal Supplies");
insert into admingroup (groupname) values("Bureau of Rules & Metaphysic");
insert into admingroup (groupname) values("The Nonspecific Bailiwick");
