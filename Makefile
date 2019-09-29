install:
	composer install

update:
	composer dump-autoload

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

phpfix:
	composer run-script phpcbf -- --standard=PSR12 src bin tests

test:
	composer run-script phpunit tests