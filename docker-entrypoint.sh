#!/bin/sh

# Generate .env file from environment variables
echo "APP_KEY=${APP_KEY}" > /app/.env
echo "APP_ENV=${APP_ENV:-production}" >> /app/.env
echo "APP_DEBUG=${APP_DEBUG:-false}" >> /app/.env
echo "APP_URL=${APP_URL:-http://localhost}" >> /app/.env
echo "AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}" >> /app/.env
echo "AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}" >> /app/.env
echo "AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION:-us-east-1}" >> /app/.env
echo "AWS_BUCKET=${AWS_BUCKET}" >> /app/.env
echo "AWS_USE_PATH_STYLE_ENDPOINT=${AWS_USE_PATH_STYLE_ENDPOINT:-false}" >> /app/.env
echo "LOG_CHANNEL=${LOG_CHANNEL:-stack}" >> /app/.env
echo "LOG_STACK=${LOG_STACK:-single}" >> /app/.env
echo "LOG_LEVEL=${LOG_LEVEL:-error}" >> /app/.env
echo "DISCORD_WEBHOOK_URL=${DISCORD_WEBHOOK_URL}" >> /app/.env
echo "CACHE_STORE=${CACHE_STORE:-file}" >> /app/.env
echo "SESSION_DRIVER=${SESSION_DRIVER:-file}" >> /app/.env

# Fix: Render sets WEB_CONCURRENCY as string, causes PHP 8.4 type error in artisan serve
export WEB_CONCURRENCY=$((WEB_CONCURRENCY + 0))

# Execute the command passed as arguments
exec "$@"
