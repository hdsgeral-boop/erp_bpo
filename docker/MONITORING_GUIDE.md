# 📊 Guia de Ativação e Gestão da Monitorização em Tempo Real (Grafana & Prometheus)

Este guia descreve como ativar, aceder e gerir a monitorização completa em tempo real para o **ERP Consulvolt**.

---

## 🚀 1. Como Ativar os Containers de Monitorização

Para iniciar o ERP Consulvolt juntamente com a stack de monitorização:

```bash
# 1. Iniciar a rede e os containers principais do ERP (se ainda não estiverem ativos)
docker-compose up -d

# 2. Iniciar a infraestrutura de monitorização (Grafana, Prometheus, cAdvisor, Redis Exporter, Node Exporter)
docker-compose -f docker-compose.monitoring.yml up -d
```

---

## 🌐 2. Endereços de Acesso

| Serviço | Porta | Descrição | Credenciais Padrão |
| :--- | :--- | :--- | :--- |
| **Grafana** | `http://localhost:3000` | Painel gráfico visual e alertas | Utilizador: `admin` / Password: `admin` |
| **Prometheus** | `http://localhost:9090` | Motor de métricas e alertas | Sem autenticação necessária localmente |
| **cAdvisor** | `http://localhost:8080` | Métricas de CPU/RAM por container | Interface web direta |
| **Redis Exporter** | `http://localhost:9121/metrics` | Métricas puras do Redis | Endpoint de métricas |

---

## 📈 3. Dashboards Disponíveis no Grafana

Ao aceder a `http://localhost:3000`, navegue até **Dashboards -> ERP Consulvolt -> ERP Consulvolt - Painel Principal de Desempenho**.

Poderá visualizar em tempo real:
1. **Consumo de CPU por Container Docker (%)**: Monitoriza `erp_backend`, `erp_horizon`, `erp_postgres`, `erp_redis`, `erp_nginx`.
2. **Uso de Memória RAM por Container (MB)**: Exibe a curva de utilização de memória de cada container.
3. **Estado da Memória do Redis (MB)**: Acompanha o consumo da cache e filas do Laravel Horizon.
4. **Clientes Redis Conectados**: Mostra quantas ligações ativas estão a comunicar com o Redis.

---

## 📱 4. Como Configurar Alertas para WhatsApp & E-mail

### A. Alertas no WhatsApp (Número Comercial: `923 012 143`)
1. Aceda ao Grafana em `http://localhost:3000`.
2. No menu lateral, aceda a **Alerting -> Contact points**.
3. Clique em **Add contact point**.
4. Defina o nome como `WhatsApp Notification`.
5. Selecione o tipo **Webhook**.
6. Insira a URL da sua API de WhatsApp (ex: `http://localhost:8000/api/v1/notifications/whatsapp` ou endpoint do gateway de mensagens).
7. Configure o payload JSON para enviar o número de destino: `+244923012143`.
8. Guarde e teste o envio enviando um *Test Alert*.

### B. Alertas por E-mail
1. No ficheiro `docker-compose.monitoring.yml`, adicione as variáveis SMTP do seu servidor ao container do Grafana:
   ```yaml
   - GF_SMTP_ENABLED=true
   - GF_SMTP_HOST=mailpit:1025 # ou smtp.consulvolt.co.ao:587
   - GF_SMTP_FROM_ADDRESS=alertas@consulvolt.co.ao
   ```
2. No Grafana, aceda a **Alerting -> Contact points -> Add contact point** -> Selecione **Email** e introduza os e-mails dos administradores.

---

## 🛠️ Comandos de Gestão Úteis

```bash
# Ver estado dos containers de monitorização
docker-compose -f docker-compose.monitoring.yml ps

# Ver logs do Grafana
docker-compose -f docker-compose.monitoring.yml logs -f grafana

# Parar a monitorização
docker-compose -f docker-compose.monitoring.yml down
```
