.PHONY: tests start stop


tests:
	./vendor/bin/phpunit --configuration phpunit.xml

start:
	./bin/console server:start

stop:
	./bin/console server:stop
