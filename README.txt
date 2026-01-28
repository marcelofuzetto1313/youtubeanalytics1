Como usar (rápido):

1) Suba este arquivo 'diagnostico.php' na MESMA pasta do seu index.php (onde fica o vendor/).
2) Acesse:
   https://SEU_DOMINIO/diagnostico.php

3) Ele vai listar:
   - Extensões que faltam (curl/json/sqlite etc.)
   - Se vendor/autoload.php existe
   - Se config.php e client_secret.json existem
   - Se storage/ é gravável (necessário para token.json e sqlite)

4) Para ver o erro 500 no index, use o 'index.php' deste pacote (patch):
   - Ele mostra o erro de config com stack trace.

IMPORTANTE: Remova os ini_set/display_errors depois de resolver (por segurança).
