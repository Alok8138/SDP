#!/bin/bash

# Start Kafka in background
/etc/confluent/docker/run &
KAFKA_PID=$!

echo "[Kafka] Waiting for broker to be ready..."
sleep 20

echo "[Kafka] Creating topics..."

kafka-topics --create \
  --if-not-exists \
  --bootstrap-server kafka:9092 \
  --topic report_data_topic \
  --partitions 3 \
  --replication-factor 1

kafka-topics --create \
  --if-not-exists \
  --bootstrap-server kafka:9092 \
  --topic report_data_dlq \
  --partitions 1 \
  --replication-factor 1

echo "[Kafka] Topics created:"
kafka-topics --list --bootstrap-server kafka:9092

# Keep container alive
wait $KAFKA_PID
