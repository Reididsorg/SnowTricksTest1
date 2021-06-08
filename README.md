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

2. Clone .env file at root project and rename it .env.local file, in order to configure environment vars.

3. Install dependancies

	- composer install

4. Install fixtures

	- symfony console doctrine:database:create
	- symfony console doctrine:migrations:diff
	- symfony console doctrine:migrations:migrate
	- symfony console doctrine:fixtures:load

- Test user : 
  login: Bruno
  mdp: 12345

Open local website in web browser :)

## Codacy Badge
