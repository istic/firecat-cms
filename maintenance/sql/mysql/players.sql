CREATE TABLE players (
    player_id integer NOT NULL AUTO_INCREMENT,
    player_name varchar(255) NOT NULL,
    player_username varchar(255) NOT NULL,
    player_password varchar(255) NOT NULL,
    email_address varchar(255) NOT NULL,
    postal_address mediumtext,
    contact_telephone varchar(255),
    emergency_telephone varchar(255),
    emergency_contact_name varchar(255),
    emergency_contact_relation varchar(255),
    date_of_birth date,
    medical_issues mediumtext,
    dietary_reqs mediumtext,
    request_pack_posted tinyint DEFAULT 0,
    requested_bunk tinyint DEFAULT 0,
    is_a_first_aider tinyint DEFAULT 0,
    general_oc_notes text,
    is_admin tinyint DEFAULT 0,
    PRIMARY KEY (player_id)
);

