create database project;
use project;
create table users(
    id int auto_increment primary key,
    name varchar(50) not null,
    email varchar(50) not null unique,
    password varchar(255) not null,
    created_at timestamp default current_timestamp,
    date_of_birth date,
    points int default 0,
    age int,
    flag boolean default false
);

insert into users(id, name, email, password, created_at, date_of_birth, points, age, flag)
values(2, 'John Doe', 'john@example.com',123456, '1-1-2025', '8-8-2008', 500, 20, 'report' );
create table admin(
    id int auto_increment primary key,
    name varchar(50) not null,
    email varchar(50) not null unique,
    password varchar(255) not null,
    date_of_birth date,
    age int,
    created_at timestamp default current_timestamp
);
create table posts(
    post_id int auto_increment primary key,
    user_id int not null,
    post_tag varchar(100) not null,
    content text not null,
    up_vote int default 0,
    down_vote int default 0,
    created_at timestamp default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    foreign key (user_id) references users(id)
);
create table bookmarks(
    bookmark_id int auto_increment primary key,
    user_id int not null,
    post_id int not null,
    created_at timestamp default current_timestamp,
    foreign key (user_id) references users(id),
    foreign key (post_id) references posts(post_id)
);
create table comments(
    comment_id int auto_increment primary key,
    post_id int not null,
    user_id int not null,
    content text not null,
    up_vote int default 0,
    down_vote int default 0,
    created_at timestamp default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    foreign key (post_id) references posts(post_id),
    foreign key (user_id) references users(id)
);
create  table badges(
badge_id int auto_increment primary key,
badge_name varchar(50) not null,
badge_description varchar(255) not null,
privilege varchar(50) not null
);
create table post_edits(
    edit_id int auto_increment primary key,
    post_id int not null,
    user_id int not null,
    edit_content text not null,
    created_at timestamp default current_timestamp,
    foreign key (post_id) references posts(post_id),
    foreign key (user_id) references users(id)
);
create table comments_edits(
    edit_id int auto_increment primary key,
    comment_id int not null,
    user_id int not null,
    edit_content text not null,
    created_at timestamp default current_timestamp,
    foreign key (comment_id) references comments(comment_id),
    foreign key (user_id) references users(id)
);
create table  notifications(
    notification_id int auto_increment primary key,
    user_id int not null,
    post_id int not null,
    comment_id int not null,
    notification_type varchar(50) not null,
    notification_message varchar(255) not null,
    created_at timestamp default current_timestamp,
    foreign key (user_id) references users(id),
    foreign key (post_id) references posts(post_id),
    foreign key (comment_id) references comments(comment_id)
);
create table user_badges(
    user_badge_id int auto_increment primary key,
    user_id int not null,
    badge_id int not null,
    created_at timestamp default current_timestamp,
    foreign key (user_id) references users(id),
    foreign key (badge_id) references badges(badge_id)
);
create table post_reports(
    report_id int auto_increment primary key,
    post_id int not null,
    user_id int not null,
    report_reason varchar(255) not null,
    created_at timestamp default current_timestamp,
    foreign key (post_id) references posts(post_id),
    foreign key (user_id) references users(id)
);
create table comment_reports(
    report_id int auto_increment primary key,
    comment_id int not null,
    user_id int not null,
    report_reason varchar(255) not null,
    created_at timestamp default current_timestamp,
    foreign key (comment_id) references comments(comment_id),
    foreign key (user_id) references users(id)
);
create table leader_board(
    leaderboard_id int auto_increment primary key,
    user_id int not null,
    total_posts int default 0,
    total_comments int default 0,
    total_recived_votes int default 0,
    created_at timestamp default current_timestamp,
    foreign key (user_id) references users(id)
);
create table  post_votes(
    vote_id int auto_increment primary key,

    post_id int not null,
    user_id int not null,
    vote_type enum('up', 'down') not null,
    created_at timestamp default current_timestamp,
    foreign key (post_id) references posts(post_id),
    foreign key (user_id) references users(id)
);
create table comment_votes(
    vote_id int auto_increment primary key,
    comment_id int not null,
    user_id int not null,
    vote_type enum('up', 'down') not null,
    created_at timestamp default current_timestamp,
    foreign key (comment_id) references comments(comment_id),
    foreign key (user_id) references users(id)
);
create table login_history(
    login_id int auto_increment primary key,# check the last login time for the  user
    user_id int not null,
    login_time timestamp default current_timestamp,
    foreign key (user_id) references users(id)
);
-- --------------------------------------------indexes ---------------------------------------------------------------------------------
# for fast access purpose
create index idx_post_votes_post_user ON post_votes(post_id, user_id);
create index idx_comments_post_user ON comments(post_id, user_id);
create index idx_notifications_user ON notifications(user_id);
create index idx_leader_board_user ON leader_board(user_id);
 -- -------------------------------------------- function to check if the user should get new badges ------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE check_and_award_badges(IN uid INT)
BEGIN
    DECLARE new_points INT;
    SELECT points INTO new_points FROM users WHERE id = uid;

    -- Helper (10 points)
    IF new_points >= 10 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Helper'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Helper';
    END IF;

    -- Sharer (100 points)
    IF new_points >= 100 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Sharer'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Sharer';
    END IF;

    -- Fixer (200 points)
    IF new_points >= 200 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Fixer'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Fixer';
    END IF;

    -- Guide (500 points)
    IF new_points >= 500 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Guide'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Guide';
    END IF;

    -- Mentor (1000 points)
    IF new_points >= 1000 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Mentor'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Mentor';
    END IF;

    -- Expert (1500 points)
    IF new_points >= 1500 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Expert'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Expert';
    END IF;

    -- Guru (2000 points)
    IF new_points >= 2000 AND NOT EXISTS (
        SELECT 1 FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.badge_id
        WHERE ub.user_id = uid AND b.badge_name = 'Guru'
    ) THEN
        INSERT INTO user_badges (user_id, badge_id)
        SELECT uid, badge_id FROM badges WHERE badge_name = 'Guru';
    END IF;
END$$

DELIMITER ;
-- ----------------------------------------------------triggers ----------------------------------------------------------------------------
# to calc the age for each user:
 -- admins and user triggers :
create trigger calc_age
before insert on users
for each row
begin
    set new.age = timestampdiff(year, new.date_of_birth, current_date);
end;
create trigger  cacl_age_admin
    before insert on admin
    for each row
    begin
        set new.age= timestampdiff(year, new.date_of_birth, current_date);
    end;

# trigger to points :
DELIMITER $$
CREATE TRIGGER vote_on_post
AFTER INSERT ON post_votes
FOR EACH ROW
BEGIN

    IF NEW.vote_type = 'up' THEN
        UPDATE users SET points = points + 10 WHERE id = NEW.user_id;
        UPDATE posts SET up_vote = up_vote + 1 WHERE post_id = NEW.post_id;
    ELSE
        UPDATE users SET points = points - 10 WHERE id = NEW.user_id;
        UPDATE posts SET down_vote = down_vote + 1 WHERE post_id = NEW.post_id;
    END IF;


    CALL check_and_award_badges(NEW.user_id);

    UPDATE leader_board
    SET total_recived_votes = total_recived_votes + 1
    WHERE user_id = NEW.user_id;

    INSERT INTO notifications(user_id, post_id, comment_id, notification_type, notification_message)
    VALUES (
        (SELECT posts.user_id FROM posts WHERE posts.post_id = NEW.post_id),
        NEW.post_id,
        NULL,
        'post_voted',
        CONCAT('Your post has been voted for ', NEW.vote_type)
    );
END$$
DELIMITER ;
-- trigger to handle votes on comments
DELIMITER $$
 create trigger vote_on_comment
AFTER INSERT ON comment_votes
     for each row
begin
    if new.vote_type = 'up' then
        update users set points = points + 5 where id = new.user_id;
        update comments set up_vote = up_vote + 1 where comment_id = new.comment_id;
    else
        update users set points = points - 5 where id = new.user_id;
        update comments set down_vote = down_vote + 1 where comment_id = new.comment_id;
    end if;

    CALL check_and_award_badges(NEW.user_id);

    update leader_board set total_recived_votes = total_recived_votes + 1 where user_id = new.user_id;

    insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
    values (
        (select comments.user_id from comments where comments.comment_id=new.comment_id),
        null,
        new.comment_id,
        'comment_voted',
        concat('Your comment has been voted for ', new.vote_type)
    );
end $$
DELIMITER ;
# trigger when admin remove user

DELIMITER $$
CREATE TRIGGER delete_user
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO notifications(user_id, post_id, comment_id, notification_type, notification_message)
    VALUES (OLD.id, NULL, NULL, 'user_deleted', CONCAT('Your account has been banned for violated the rules'));
    DELETE FROM posts WHERE user_id = OLD.id;
    DELETE FROM comments WHERE user_id = OLD.id;
    DELETE FROM post_votes WHERE user_id = OLD.id;
    DELETE FROM comment_votes WHERE user_id = OLD.id;
    DELETE FROM post_reports WHERE user_id = OLD.id;
    DELETE FROM comment_reports WHERE user_id = OLD.id;
    DELETE FROM notifications WHERE user_id = OLD.id;
    DELETE FROM login_history WHERE user_id = OLD.id;
END $$
DELIMITER ;

create trigger  flagged
    after update on users
    for each row
    begin
        if new.flag = true then
            insert into notifications(user_id, post_id, comment_id, notification_type, notification_message) values(old.id, null, null, 'user_flagged', concat('Your account has been flagged'));
        end if;
    end;
-- ----------------------------------------------------posts and comments----------------------------------------------------------------------------
# trigger to handle the post count for each user
create trigger post_count
    after insert on posts
    for each row
    begin
        update users set points = points + 5 where id = new.user_id;
        update leader_board set total_posts = total_posts + 1    where user_id = new.user_id;
           CALL check_and_award_badges(NEW.user_id);
    end;

create trigger post_delete
    after delete on posts
    for each row
    begin
        update users set points = points - 5 where id = old.user_id;
        update leader_board set total_posts = total_posts - 1 where user_id = old.user_id;
    end;

# trigger to handle the comment count for each user
create trigger comment_count
    after insert on comments
    for each row
    begin
        update  users set points =points+2 where id = new.user_id;
        update leader_board set total_comments = total_comments+1 where user_id= new.user_id;
        insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
        values ((select posts.user_id from posts where posts.post_id=new.post_id),
                new.post_id,
                new.comment_id,
                'new_comment',
                concat('New comment added on your post  ', new.post_id));
        CALL check_and_award_badges(NEW.user_id);
    end;
-- edits triggers :
create trigger edits_onpost
    after insert on post_edits
    for each row
    begin
        update posts set content = new.edit_content where post_id = new.post_id;
        insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
        values ((select posts.user_id from posts where posts.post_id=new.post_id), new.post_id, null, 'post_edited',
                concat('Your post has been edited  ', new.post_id));
    end;

create trigger edits_comments
    after  insert on comments_edits
    for each row
    begin
            update comments set content=new.edit_content where comment_id=new.comment_id;
            insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
                values ((select comments.user_id from comments where comments.comment_id=new.comment_id), null, new.comment_id, 'comment_edited',
                        concat('Your comment has been edited  ', new.comment_id));
    end;

-- babge notifcations :
create trigger get_badge
    after insert on user_badges
    for each row
begin
    insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
    values (new.user_id, null, null, 'badge_awarded', concat('You have been awarded the badge: ',
    (select badge_name from badges where badge_id = new.badge_id)));
end;
-- reports :
create trigger report_on_post
    after insert on post_reports
    for each row
    begin
        insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
            values (new.user_id,
                    new.post_id,
                    null,
                    'post_reported',
                    concat('Your post has been reported for ',
                      new.report_reason));
    end;
create  trigger report_on_comments
    after insert on comment_reports
    for each row
    begin
        insert into notifications(user_id, post_id, comment_id, notification_type, notification_message)
            values (new.user_id,
                    null,
                    new.comment_id,
                    'comment_reported',
                    concat('Your comment has been reported for ',
                           new.report_reason));
    end;
-- trigger to check last login time for each user
DELIMITER $$

CREATE TRIGGER last_login
AFTER INSERT ON login_history
FOR EACH ROW
BEGIN
  DECLARE previous_login_days INT;

  SELECT DATEDIFF(CURRENT_DATE, login_time)
  INTO previous_login_days
  FROM login_history
  WHERE user_id = NEW.user_id
  ORDER BY login_time DESC
  LIMIT 1 OFFSET 1;
  IF previous_login_days > 30 THEN
    IF (SELECT flag FROM users WHERE id = NEW.user_id) = TRUE THEN
      DELETE FROM users WHERE id = NEW.user_id;
      INSERT INTO notifications(user_id, post_id, comment_id, notification_type, notification_message)
      VALUES (NEW.user_id, NULL, NULL, 'user_banned', 'Your account has been permanently banned due to inactivity.');
       ELSE
      UPDATE users SET flag = TRUE WHERE id = NEW.user_id;
      INSERT INTO notifications(
      user_id
    , post_id,
      comment_id,
      notification_type,
      notification_message)
      VALUES (NEW.user_id, NULL, NULL, 'user_flagged', 'Your account has been flagged due to inactivity.');
    END IF;
  END IF;
END$$
DELIMITER ;
select * from badges;
select * from users;
insert into users (name, email, password, date_of_birth) values
('mahmoud', 'mahmoud@gmail.com',123456, '1998-01-01');
ALTER TABLE notifications MODIFY post_id INT NULL;
ALTER TABLE notifications MODIFY comment_id INT NULL;

ALTER TABLE notifications DROP
foreign key notifications_ibfk_1;
alter table notifications
ADD CONSTRAINT notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;