.PHONY: tests start stop diffdump migrate


tests:
	./vendor/bin/phpunit --configuration phpunit.xml

start:
	./bin/console server:start

stop:
	./bin/console server:stop

diffdump:
	./bin/console doctrine:schema:update --dump-sql

migrate:
	./bin/console doctrine:migrations:migrate --no-interaction

test_migrate:
	./bin/console doctrine:migrations:migrate --no-interaction --env=test

test_db:
	mysql -uroot -e "DROP DATABASE IF EXISTS shopping_api_alpha_test"
	mysql -uroot -e "CREATE DATABASE shopping_api_alpha_test"
