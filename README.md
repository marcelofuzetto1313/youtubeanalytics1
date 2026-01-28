# Meu Analytics (PHP) — YouTube Analytics API + YouTube Data API
Hospedagem-alvo: **youtubestats.free.nf**

Este projeto cria o seu “meu analytics” do jeito **oficial**:
- Puxa métricas **diárias** via **YouTube Analytics API** (reports.query)
- Puxa publicações (uploads) via **YouTube Data API v3**
- Salva tudo em **SQLite** e gera relatórios (JSON/CSV) com agregações (dia da semana etc.)

## O que você consegue responder
### Via Analytics (oficial, diário)
- Views por dia
- Likes por dia
- Inscritos ganhos por dia (subscribersGained)

### Via Data API (publicações, diário)
- Quantidade de vídeos publicados por dia
- Quantidade de Shorts publicados por dia (**estimado**)
  - Critério padrão: duração <= 60s OU hashtag #shorts no título/descrição

> Importante: não existe “hora do dia” no Analytics oficial; o menor grão oficial é **day**.

---

# Onde encontrar as infos / configurar APIs do seu canal
Tudo é feito no **Google Cloud Console** (mesmo lugar onde você cria credenciais e habilita APIs):

1. **Criar projeto** no Google Cloud Console
2. Em **APIs & Services → Library**, habilite:
   - **YouTube Analytics API**
   - **YouTube Data API v3**
3. Em **APIs & Services → OAuth consent screen**:
   - configure o app (External/Interno)
4. Em **APIs & Services → Credentials**:
   - crie **OAuth client ID** (tipo *Web application*)
   - defina **Authorized redirect URI**:
     - `https://youtubestats.free.nf/oauth2callback.php`
5. Baixe o JSON do OAuth e salve na raiz do projeto como:
   - `client_secret.json`

Documentação oficial:
- OAuth para apps web: https://developers.google.com/youtube/v3/guides/auth/server-side-web-apps
- Analytics reports.query: https://developers.google.com/youtube/analytics/reference/reports/query
- Data API channels.list: https://developers.google.com/youtube/v3/docs/channels/list
- Data API playlistItems.list: https://developers.google.com/youtube/v3/docs/playlistItems/list
- Data API videos resource (duration etc.): https://developers.google.com/youtube/v3/docs/videos

---

# Deploy no youtubestats.free.nf
1) Envie o conteúdo de `public/` para o **document root** do seu site (geralmente `htdocs/`).
2) Envie as pastas `src/`, `vendor/` e `storage/` para fora do public (se o host permitir) **ou** mantenha na mesma raiz do projeto.
3) Execute `composer install` (no seu PC) e faça upload da pasta `vendor/`.

> Se seu host não permite rodar composer no servidor: rode local e faça upload do projeto já com `vendor/`.

---

# Segurança (IMPORTANTE)
O endpoint `/sync.php` é protegido por uma chave:
- **SYNC_KEY** em `public/config.php` (troque imediatamente!)
- Exemplo: `https://youtubestats.free.nf/sync.php?key=...`

Chave padrão gerada neste zip:
- `CHANGE_ME_9fc8909a67155d3c0f9df04c`

Troque assim que instalar.

---

# Fluxo de uso
1. Acesse: `https://youtubestats.free.nf/`
2. Clique em **Conectar com Google** e autorize
3. Rode o sync:
   - `https://youtubestats.free.nf/sync.php?key=SEU_SYNC_KEY&days=90`
4. Veja relatório:
   - `https://youtubestats.free.nf/report.php?days=90`
   - CSV: `https://youtubestats.free.nf/report.php?days=90&format=csv`

## Agendar atualização diária
Se o host não tiver cron, use um serviço externo (ex.: cron-job.org) para chamar:
- `https://youtubestats.free.nf/sync.php?key=SEU_SYNC_KEY&days=7`
(ou `days=30`). O sync é idempotente (faz UPSERT por data).

---

# Banco (SQLite)
Arquivo: `storage/youtubestats.sqlite`

Tabelas:
- `daily_analytics` (date, views, likes, subscribers_gained)
- `daily_uploads` (date, videos_published, shorts_estimated)

---

# Ajustes
Em `public/config.php`:
- TIMEZONE (padrão America/Sao_Paulo)
- SHORTS_DETECT_MODE
- SHORTS_MAX_SECONDS
- SYNC_KEY
