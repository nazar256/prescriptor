include .makerc

bootstrap: destroy env build
	docker-compose run --rm server bin/console app:fixtures-load:diseases ${DISEASES_AMOUNT} ${DRUGS_PER_DISEASE}
env:
	cp .env.dist .env;
	echo "" >> .env
	echo "APP_SECRET="`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1` >> .env;
	echo "MYSQL_ROOT_PASSWORD="`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1` >> .env;
	echo "MYSQL_PASSWORD="`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1` >> .env;
	echo "PUID="`id -u` >> .env;

build:
	docker-compose build
	docker-compose run --rm client composer install
	# TODO: implement better mysql up status check (i.e wait-for script)
	# steps above may be executed faster than mysql actually starts, sleep should be fine for this taks right now
	sleep 15
	docker-compose run --rm server composer install
	docker-compose run --rm server ./vendor/bin/rr get -n
	docker-compose run --rm server bin/console cache:warmup
	docker-compose run --rm server bin/console doctrine:migrations:migrate -n

load-test:
#	docker-compose up -d --scale server=4 nginx
#	docker-compose run --rm client bin/cli.php 'http://nginx:80/diseases/{{ID}}?include=drugs&patient={{NAME}}' 1 ${DISEASES_AMOUNT} --ttl=${PRESCRIPTION_TIMEOUT_MS} --duration=300 --concurrency=${TEST_CONCURRENCY}
	docker-compose up -d --scale roadrunner=4 balancer
	docker-compose run --rm client bin/cli.php 'http://balancer:80/diseases/{{ID}}?include=drugs&patient={{NAME}}' 1 ${DISEASES_AMOUNT} --ttl=${PRESCRIPTION_TIMEOUT_MS} --duration=300 --concurrency=${TEST_CONCURRENCY}

destroy:
	docker-compose down --remove-orphans
	rm -Rf ./server/var/cache/*
