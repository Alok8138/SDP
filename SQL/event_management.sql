-- create database event_management_system;
use event_management_system;

-- users
create table users(
    user_id bigint primary key auto_increment,
    user_name varchar(128) not null,
    email varchar(64) unique not null,

    created_by bigint null,
    created_at timestamp default current_timestamp,
    updated_by bigint null,
    updated_at timestamp default current_timestamp on update current_timestamp,
    deleted_by bigint null,
    deleted_at timestamp null
);

-- organizers
create table organizers(
    org_id bigint primary key auto_increment,
    org_name varchar(128) not null,
    email varchar(64) unique not null,

    created_by bigint,
    created_at timestamp default current_timestamp,
    updated_by bigint,
    updated_at timestamp default current_timestamp on update current_timestamp,
    deleted_by bigint,
    deleted_at timestamp null,

    foreign key (created_by) references users(user_id),
    foreign key (updated_by) references users(user_id),
    foreign key (deleted_by) references users(user_id)
);

-- venues
create table venues(
    venue_id bigint primary key auto_increment,
    venue_name varchar(128) not null,
    location text not null,
    capacity int not null,

    created_by bigint,
    created_at timestamp default current_timestamp,
    updated_by bigint,
    updated_at timestamp default current_timestamp on update current_timestamp,
    deleted_by bigint,
    deleted_at timestamp null,

    foreign key (created_by) references users(user_id),
    foreign key (updated_by) references users(user_id),
    foreign key (deleted_by) references users(user_id)
);

-- events
create table events(
    event_id bigint primary key auto_increment,
    org_id bigint,
    venue_id bigint,

    title varchar(256) not null,
    event_description text not null,
    event_date date not null,

    event_status enum(
		'upcoming',
        'ongoing',
        'completed',
        'cancelled'
    ) default 'upcoming',

    event_duration_minutes int not null,

    created_by bigint,
    created_at timestamp default current_timestamp,
    updated_by bigint,
    updated_at timestamp default current_timestamp on update current_timestamp,
    deleted_by bigint,
    deleted_at timestamp null,

    foreign key (org_id) references organizers(org_id),
    foreign key (venue_id) references venues(venue_id),
    foreign key (created_by) references users(user_id),
    foreign key (updated_by) references users(user_id),
    foreign key (deleted_by) references users(user_id)
);

-- drop database event_management_system;
-- event tickets
create table event_tickets(
    ticket_id bigint primary key auto_increment,
    event_id bigint,

    price decimal(10,2) not null,
    quantity int not null,
    sold_quantity int default 0,

    foreign key (event_id) references events(event_id)
);

-- registrations
create table registrations(
    registration_id bigint primary key auto_increment,
    user_id bigint,
    ticket_id bigint,
    quantity int not null,

    created_at timestamp default current_timestamp,

    foreign key (user_id) references users(user_id),
    foreign key (ticket_id) references event_tickets(ticket_id)
);

-- payments
create table payments(
    payment_id bigint primary key auto_increment,
    registration_id bigint,

    amount decimal(10,2) not null,

    payment_status enum(
        'pending',
        'success',
        'failed'
    ) default 'pending',

    payment_date timestamp default current_timestamp,

    foreign key (registration_id) references registrations(registration_id)
);

-- favorite events
create table favorite_events(
    user_id bigint,
    event_id bigint,

    primary key(user_id, event_id),

    foreign key (user_id) references users(user_id),
    foreign key (event_id) references events(event_id)
);


select * from users;
select * from organizers;
select * from venues;
select * from events;
select * from event_tickets;
select * from registrations;
select * from payments;
select * from favorite_events;

 
 
 
