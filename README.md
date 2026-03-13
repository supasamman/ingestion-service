# Log Ingestion Service

API-сервис для сбора логов от агентов и публикации в RabbitMQ.

## Требования

- Docker
- Docker Compose
- RabbitMQ

## Запуск

```bash
# 1. Клонируй репо
git clone <repo> && cd <project_name>
 
# 2. Запусти команду
make init
```

## API

### POST /api/logs/ingest

```bash
curl -X POST http://localhost:8080/api/logs/ingest \
  -H "Content-Type: application/json" \
  -d '{
    "logs": [
      {
        "timestamp": "2026-02-26T10:30:45Z",
        "level": "error",
        "service": "auth-service",
        "message": "User authentication failed",
        "context": {"user_id": 123},
        "trace_id": "abc123"
      }
    ]
  }'
```

**202 Accepted:**
```json
{
  "status": "accepted",
  "batch_id": "batch_0195e1b0-...",
  "logs_count": 1
}
```

**400 Bad Request:**
```json
{
  "status": "error",
  "errors": ["logs[0].level: Invalid value \"value\""]
}
```

## Тесты

```bash
make test
```

## RabbitMQ

Management UI: http://localhost:15672 (app / app)

Очередь `logs.ingest` — тип `direct`, durable. 
Сообщения отправляются батчем с приоритетом на основе максимального уровня лога в батче.