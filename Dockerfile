FROM postgres:15

# Instalar dependências necessárias
RUN apt-get update && apt-get install -y \
    build-essential \
    postgresql-server-dev-15 \
    git \
    && rm -rf /var/lib/apt/lists/*

# Clonar e instalar o pgvector
RUN git clone --branch v0.5.1 https://github.com/pgvector/pgvector.git \
    && cd pgvector \
    && make \
    && make install \
    && cd .. \
    && rm -rf pgvector

# Script para criar a extensão ao iniciar
COPY docker/postgres/init-scripts/01-create-extension.sh /docker-entrypoint-initdb.d/
RUN chmod +x /docker-entrypoint-initdb.d/01-create-extension.sh 