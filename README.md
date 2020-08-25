## Computer Science Learning Platform

Computer science class in school education
covers a variety of topics (text processing,
coding, data privacy, social aspects, etc.). The
web platform contains all these learning
objectives based on standardized computer
science curricula. An engaging story serves as a
starting point for these objectives. All tasks are
related to the narrative and use gamification
elements to create a learner-friendly
environment. The learning platform should
support both students and teachers in an
educational context via learning, assessment
and scoring. 


##Setup

Use the docker container!

Start: `docker-compose up`

Webapp is running on: http://localhost/

Login:

E-Mail: `admin@mail.com`
Password: `password`

---

**Initial setup**

* Create .env file : `mv .env.example .env`

* Generate key: `docker-compose exec php php artisan key:generate`

* Install PHP libraries using composer: `docker-compose exec php composer install` 

* Fix folder permission: `docker-compose exec php chmod 777 -R storage/` 

* Initial database setup: `docker-compose exec php php artisan migrate:fresh --seed`

* Create symlink for user-uploaded files: `docker-compose exec php php artisan storage:link`



