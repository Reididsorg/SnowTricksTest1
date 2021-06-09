# SnowTricksTest1

Snowtricks is a community website, with aim to popularize and learn snowboard by sharing snowboard tricks.

## Development environment
PHP 7.4
MySQL 8.0.25
Symfony 4.23.5
Symfony server
Linux Ubuntu 
Composer 1.11.99
Bootstrap 4.6.0
jQuery 3.5.1

## Installation

1. Clone Github repository

	- git clone https://github.com/Reididsorg/SnowTricksTest1.git

2. Clone '.env' file at root project and rename it '.env.local', in order to configure environment vars (especially DATABASE_URL and MAILER_URL)

3. Install dependancies

	- composer install

4. Install fixtures

	- bin/console doctrine:database:create (or manually create database)
	- bin/console doctrine:migrations:diff
	- bin/console doctrine:migrations:migrate
	- bin/console doctrine:fixtures:load

- Test user : 
  login: Bruno
  mdp: 12345

Open local website in web browser :)

## Codacy Badge
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/789ea9a0b64d4f088b2f627e00e9bc7e)](https://www.codacy.com/gh/Reididsorg/SnowTricksTest1/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Reididsorg/SnowTricksTest1&amp;utm_campaign=Badge_Grade)
