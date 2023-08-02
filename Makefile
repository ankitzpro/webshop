setup:
	@make build
	@make up 
	@make composer-install
build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop
up:
	docker-compose up -d
composer-install:
	docker exec laravel-docker bash -c "composer install"
data:
	docker exec laravel-docker bash -c "php artisan migrate"