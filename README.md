# bailiff-panel-server

Simple PHP REST API for the "Panel komornika" QA recruitment exercise.

## Requirements

- PHP 8.2+
- Composer

## Install

```bash
composer install
```

## Run

```bash
composer start
```

The API is available at:

```text
http://localhost:8080
```

## Endpoints

- `GET /api/bailiffs`
- `GET /api/bailiffs?search=kowalski`
- `GET /api/bailiffs/{id}`
- `POST /api/bailiffs`
- `PUT /api/bailiffs/{id}`
- `GET /api/random-error`
- `GET /docs/openapi.yaml`

## Example request

```bash
curl -X POST http://localhost:8080/api/bailiffs \
  -H "Content-Type: application/json" \
  -d '{"firstName":"Anna","lastName":"Nowak","courtName":"Sąd Rejonowy w Krakowie","officeAddress":"ul. Karmelicka 12, 31-128 Kraków","email":"anna.nowak@example.pl","phone":"+48 502 345 678","status":"active"}'
```

## Data

The API stores records in `data/bailiffs.json`. If the file is missing, it is initialized from `data/bailiffs.seed.json`.

To reset local data, replace `data/bailiffs.json` with the content of `data/bailiffs.seed.json`.

## OpenAPI

The static OpenAPI document is stored in `docs/openapi.yaml` and is also served from:

```text
http://localhost:8080/docs/openapi.yaml
```

## Tests

```bash
composer test
```

The tests are plain PHP scripts, so the project does not need PHPUnit for the recruitment exercise.
