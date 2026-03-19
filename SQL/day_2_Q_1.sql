create database sql_2;

use sql_2;

create table users(
	user_id bigint primary key auto_increment,
    user_name varchar(64) not null
);

create table user_activity(
	activity_id bigint primary key auto_increment,
    user_id bigint,
    activity_name varchar(128) not null,
    created_at datetime default current_timestamp,
    
    foreign key (user_id) references users(user_id)
);


insert  into users(user_name) values ("om"),("manthan"),("kavyan");

select * from users;
select * from user_activity;



insert into user_activity(user_id,activity_name) values(1,"home page");



-- first calculate gap 

-- now we got the previous session time and current session time now create new table where pre - crr > 30 min and mark it as 1 else 0 0:session is not expired , 1: session is expired
with activity_gap as (
	select user_id,
    created_at ,
    lag(created_at) over(partition by user_id order by created_at) as previous_activity_time
    
    from user_activity
),
session_marker as (
	select *,case  
				when previous_activity_time is null then 1
                when timestampdiff(minute,previous_activity_time,created_at) > 30 then 1
                else 0
			end as is_session_expire
	from activity_gap
),

session_id_assignment as (

	select user_id,created_at, sum(is_session_expire) over(partition by user_id order by created_at) as session_id
    from session_marker
)

select user_id,min(created_at) as session_start,max(created_at) as session_end,timestampdiff(minute,min(created_at),max(created_at)) as duration, 
		count(*) as activity_count,timestampdiff(minute,min(created_at),max(created_at)) > 30  as is_session_expired,session_id
        from session_id_assignment group by user_id,session_id;
        

-- select * from user_activity;

-- update user_activity set created_at = now() where user_id = 2 and activity_id = 4;

