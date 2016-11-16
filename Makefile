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
