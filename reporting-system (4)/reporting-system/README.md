# Dynamic Reporting System

A full-stack dynamic reporting platform built with **React + PHP MVC + Apache Kafka + Apache Solr**, fully containerized with Docker Compose.

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (v4+)
- Docker Compose v2 (`docker compose version`)
- ~4 GB free RAM for all services

---

## Quick Start

```bash
# 1. Clone and enter the project
git clone <your-repo-url> reporting-system
cd reporting-system

# 2. Copy environment file
cp .env.example .env

# 3. Build and start all services (first run takes 3–5 min)
docker compose up -d --build

# 4. Wait for Solr to initialize (~20 seconds), then verify
docker compose ps
```

All services should show `running`. Open the app at **http://localhost**.

---

## Import Sample Data

```bash
# Step 1: Send CSV rows to Kafka
docker exec -it php php scripts/import_csv.php /var/www/sample/sample_data.csv

# Step 2: Start the Kafka → Solr consumer (runs as daemon)
docker exec -it php php app/Kafka/Consumer.php
```

Wait ~5 seconds for Solr to soft-commit the documents, then refresh the UI.

---

## Service URLs

| Service       | URL                              |
|---------------|----------------------------------|
| React UI      | http://localhost                 |
| PHP API       | http://localhost/api             |
| Solr Admin    | http://localhost:8983/solr       |
| Kafka Broker  | localhost:9092                   |
| Redis         | localhost:6379                   |
| MySQL         | localhost:3306                   |

---

## API Endpoints

| Method | Endpoint                  | Description                        |
|--------|---------------------------|------------------------------------|
| GET    | /api/reports              | Paginated report rows              |
| GET    | /api/reports/schema       | Server-driven column schema        |
| GET    | /api/facets/{field}       | Solr facet values for a field      |
| GET    | /api/charts               | Aggregated chart data              |
| GET    | /api/saved-views          | List saved views                   |
| POST   | /api/saved-views          | Create a saved view                |
| PUT    | /api/saved-views/{id}     | Update a saved view                |
| DELETE | /api/saved-views/{id}     | Delete a saved view                |
| GET    | /api/column-config        | Get user column config             |
| PUT    | /api/column-config        | Save user column config            |
| POST   | /api/date-compare         | Compare two date ranges            |

---

## Project Structure

```
reporting-system/
├── docker-compose.yml
├── .env.example
├── nginx/               # Reverse proxy config
├── backend/             # PHP 8.2 MVC API
│   ├── app/
│   │   ├── Controllers/ # Request handlers
│   │   ├── Services/    # SolrQueryBuilder, CacheService, etc.
│   │   ├── Kafka/       # Producer + Consumer daemons
│   │   └── Core/        # Router, Request, Response, Database
│   └── scripts/         # CLI import script
├── frontend/            # React 18 + Vite + TanStack + Recharts
│   └── src/
│       ├── components/  # FilterBuilder, DataTable, ChartRenderer, etc.
│       ├── store/       # Zustand state
│       ├── hooks/       # TanStack Query hooks
│       └── api/         # Axios API clients
├── solr/                # Solr 9 schema + config
├── mysql/               # MySQL init schema
└── sample/              # 500-row sample CSV
```

---

## Useful Commands

```bash
# View logs for a specific service
docker compose logs -f php
docker compose logs -f solr

# Restart a single service
docker compose restart php

# Open a shell in PHP container
docker exec -it php bash

# Check Kafka topics
docker exec -it kafka kafka-topics --list --bootstrap-server localhost:9092

# Stop all services (keeps volumes)
docker compose down

# Stop and delete all data volumes
docker compose down -v
```

---

## Features

- **Dynamic Column Selector** — show/hide and drag-reorder columns; preferences persisted per user
- **Advanced Filter Builder** — AND/OR nested filter tree with text, dropdown, number range, date range, boolean types; Solr facet-backed autocomplete
- **Data Table** — virtual scroll (handles 100k+ rows), resizable columns, cursor-based pagination, sortable headers
- **Chart Renderer** — bar, line, pie charts via Recharts; click to drill down; export as PNG
- **Date Compare** — compare any two date ranges; shows % change and absolute diff per metric
- **Saved Views** — save column + filter + sort state as named views; set default; share with team; versioned

---

## Tech Stack

| Layer     | Technology                                      |
|-----------|-------------------------------------------------|
| Frontend  | React 18, Vite, TanStack Query/Table, Zustand, Recharts, Tailwind CSS |
| Backend   | PHP 8.2 (custom MVC), Redis cache               |
| Pipeline  | Apache Kafka 7.5, php-rdkafka                   |
| Search    | Apache Solr 9.4 (facets, cursor pagination)     |
| Database  | MySQL 8 (saved views, column config, audit log) |
| DevOps    | Docker Compose, Nginx                           |
