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

## CORS

Allowed frontend domains can be configured with the `ALLOWED_ORIGINS` environment variable:

```text
ALLOWED_ORIGINS=https://komornik.kula.wroclaw.pl,http://localhost:5173
```

Use comma-separated origins without trailing slashes. If the variable is missing, the server allows:

```text
http://localhost:5173
http://127.0.0.1:5173
https://komornik.kula.wroclaw.pl
```

For the planned hosting setup, keep the frontend origin in this list:

```text
https://komornik.kula.wroclaw.pl
```

## Endpoints

- `GET /api/bailiffs`
- `GET /api/bailiffs?search=kowalski`
- `GET /api/bailiffs/{id}`
- `POST /api/bailiffs`
- `PUT /api/bailiffs/{id}`
- `POST /api/pull-public-data`
- `GET /api/docs`
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

Swagger UI is available at:

```text
http://localhost:8080/api/docs
```

It loads the static OpenAPI document from:

```text
http://localhost:8080/docs/openapi.yaml
```

The OpenAPI server URL is relative (`/`), so Swagger calls the same host that serves `/api/docs`. Locally it calls `http://localhost:8080`, and on hosting it calls `https://komornik-api.kula.wroclaw.pl`.

The Swagger UI page uses CDN-hosted Swagger assets and allows calling the API endpoints directly through "Try it out".

`POST /api/pull-public-data` returns one of the recruitment-test statuses: `205`, `400`, `401`, `403`, `404`, `429`, `500`, `502`, `503`. The response message is returned in the JSON body where the HTTP client supports it and in the `X-Public-Data-Message` header. The header is URL-encoded because `205 Reset Content` responses are commonly treated as bodyless by clients.

## Tests

```bash
composer test
```

The tests are plain PHP scripts, so the project does not need PHPUnit for the recruitment exercise.
