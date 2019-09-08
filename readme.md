```shell script
docker run --rm --interactive --tty --volume ${PWD}:/app composer install --ignore-platform-reqs --no-scripts
docker exec cz_php php artisan migrate

docker-compose -f "docker/docker-compose.yml" up -d
docker-compose -f "docker/docker-compose-node.yml" up
```
