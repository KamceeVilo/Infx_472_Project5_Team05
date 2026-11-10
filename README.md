# INFX 472 Project 5 - Social Media Website

## Project Overview

This project is a social media website created for **INFX 472 Project 5**.

The application will allow users to:

- Register for an account
- Log in and log out
- Maintain an authenticated session
- View a personalized newsfeed
- Create posts
- View user profiles
- View posts from themselves and their friends
- Send and manage friend requests
- View profile statistics such as posts, likes, and friends

The project is being developed using:

- PHP
- MySQL
- HTML
- CSS
- JavaScript

---

## Team Members

**Team 05**

- Kamsy Ezevillo
- Zachary Taylor
- Dillon

---

## Team Responsibilities

### Kamsy - Registration and Authentication Foundation

Primary files:

- `register.php`
- `common.php`

Responsibilities:

- Registration form processing
- Trimming user input
- First name and last name validation
- Username validation
- Email validation
- Email confirmation
- Password handling
- Shared session functionality
- Authentication helper functions

---

### Zach - Database and Social Backend

Responsibilities:

- Database design
- Database connection
- User data
- Posts
- Likes
- Friend relationships
- Friend requests
- Backend queries
- SQL database export

---

### Dillon - Interface and Design

Responsibilities:

- Overall visual design
- `style.css`
- Shared site header
- Login and registration page styling
- Newsfeed layout
- Profile page layout
- Logo and visual branding

---

## Planned Project Files

- `index.php`
- `header.php`
- `login.php`
- `register.php`
- `logout.php`
- `common.php`
- `requests.php`
- `profile.php`
- `style.css`
- `db.php`

Additional files may be added as development continues.

---

## Authentication

The application will use PHP sessions to determine whether a user is authenticated.

Pages that require authentication will redirect users to `login.php` when no active login session exists.

The registration system will validate user information before accounts are created.

Usernames and email addresses will be unique for each account.

---

## Registration

The registration page will collect:

- First name
- Last name
- Username
- Email address
- Email confirmation
- Password

User input will be validated before information is stored in the database.

Profile pictures will be randomly assigned from the supplied profile images.

---

## Newsfeed

The main `index.php` page will function as the user's newsfeed.

The newsfeed will display:

- Logged-in user's profile information
- User profile picture
- Number of posts
- Number of likes
- Post creation form
- Posts from the logged-in user
- Posts from friends
- Poster name and profile picture
- Time since each post was created

Newest posts will be displayed first.

---

## User Profiles

Each user will have a profile page displaying:

- Profile picture
- First and last name
- Number of posts
- Number of likes
- Number of friends
- User's posts

Users will be able to view other users' profile pages.

---

## Friend Requests

The application will include friend request functionality through `requests.php`.

Friend relationships will also help determine which posts appear in a user's newsfeed.

---

## Current Progress

- GitHub repository created
- Team responsibilities assigned
- Initial project structure created
- `register.php` started
- Registration validation started
- `common.php` created
- Shared session/authentication helper functions started
- Database and interface development beginning in parallel

---

## GitHub Workflow

1. Pull the latest version of `main`
2. Create or switch to your assigned branch
3. Make and test changes locally
4. Commit changes with a descriptive message
5. Push the branch to GitHub
6. Create a pull request
7. Review the changes
8. Merge approved changes into `main`

---

## Repository
**INFX 472 - Project 5 - Team 05**
This repository contains the source code and supporting files for the Team 05 social media application.
