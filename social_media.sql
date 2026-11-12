-- Social Media Schema (MySQL 8+)
-- Generated from social_media_schema.drawio

CREATE DATABASE IF NOT EXISTS social_media
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE social_media;

-- Drop in reverse dependency order so the script can be re-run
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

-- users
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  username      VARCHAR(30)  NOT NULL,
  email         VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_users_username UNIQUE (username),
  CONSTRAINT uq_users_email    UNIQUE (email)
) ENGINE=InnoDB;

-- posts
-- user_id = author, page_id = the user whose page the post appears on
CREATE TABLE posts (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT       NOT NULL,
  page_id    INT       NOT NULL,
  content    TEXT      NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_posts_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_posts_page FOREIGN KEY (page_id)
    REFERENCES users (id) ON DELETE CASCADE,
  INDEX idx_posts_user (user_id),
  INDEX idx_posts_page (page_id)
) ENGINE=InnoDB;

-- comments
CREATE TABLE comments (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  post_id    INT       NOT NULL,
  user_id    INT       NOT NULL,
  content    TEXT      NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_post FOREIGN KEY (post_id)
    REFERENCES posts (id) ON DELETE CASCADE,
  CONSTRAINT fk_comments_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE,
  INDEX idx_comments_post (post_id),
  INDEX idx_comments_user (user_id)
) ENGINE=InnoDB;

-- likes (composite primary key: a user can like a post only once)
CREATE TABLE likes (
  user_id    INT       NOT NULL,
  post_id    INT       NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, post_id),
  CONSTRAINT fk_likes_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_likes_post FOREIGN KEY (post_id)
    REFERENCES posts (id) ON DELETE CASCADE,
  INDEX idx_likes_post (post_id)
) ENGINE=InnoDB;