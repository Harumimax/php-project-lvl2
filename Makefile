install:
	composer install

update:
	composer dump-autoload

lint:
	composer run-script phpcs -- --standard=PSR12 src bin

phpfix:
	composer run-script phpcbf -- --standard=PSR12 src bin

test:
	composer run-script phpunit tests

push:
	git push origin master