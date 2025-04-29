import { check, sleep } from 'k6';
import http from 'k6/http';

export const options = {
  vus: 5,           // Número fixo de 5 usuários virtuais
  duration: '1m',   // Duração do teste de 1 minuto
  thresholds: {
    http_req_duration: ['p(95)<5000'], // Aumentado para 5 segundos devido à natureza do streaming
    http_req_failed: ['rate<0.01'],    // Menos de 1% das requisições podem falhar
  },
};

const token = '2|lY3Rh90uM5B31R2IjDuWYa8mD4UbTl2O1nGZBClwda83ab45';
const baseUrl = 'http://localhost:8002';

export default function () {
  const params = {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'text/event-stream',
      'Cache-Control': 'no-cache',
      'Connection': 'keep-alive',
    },
    timeout: '60s', // Timeout aumentado para permitir o streaming
  };

  const res = http.get(`${baseUrl}/api/gpt/stream-partes?instrucoes=retire%20as%20partes%20do%20processo&numero_cnj=1239786739012876329187623`, params);

  check(res, {
    'status is 200': (r) => r.status === 200,
    'content type is text/event-stream': (r) => r.headers['Content-Type'].includes('text/event-stream'),
    'received SSE data': (r) => r.body.length > 0,
  });

  // Aguarda um tempo entre as requisições para não sobrecarregar o servidor
  sleep(5);
} 