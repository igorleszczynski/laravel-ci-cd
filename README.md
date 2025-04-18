## Budowanie obrazu
```
docker buildx build -t laravel-ci-cd:main -f scripts/docker/Dockerfile .
```
### Uruchamianie kontenerów
```
docker compose --env-file=.env.production -f docker-compose.prod.yml up -d --force-recreate
```
