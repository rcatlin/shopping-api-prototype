# Shopping API Alpha

## Requirements

* Running version of MySQL
* PHP >= 5.6
* `composer` in your `$PATH`

## Setup

* `git clone git@github.com:rcatlin/shopping-api-alpha` - Clone the repository
* `cd shopping-api-alpha` - Enter the repository's directory 
* `composer install` - Install PHP Dependencies
* `./bin/symfony_requirements` - Ensure your environment has the correct requirements for Symfony
* `make database` - Create the Database and it's Schema
* `make tests` - Run the Test Suite
* `make start` - Start the Server
* Visit `localhost:8000/api/doc` in your browser to view the API Documentation
