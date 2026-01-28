DEPLOY (InfinityFree / htdocs)

Upload EVERYTHING in this zip to /htdocs.

Also upload these folders from your local project to /htdocs:
- vendor/
- src/
- storage/

Expected:
htdocs/
  index.php
  login.php
  oauth2callback.php
  sync.php
  report.php
  config.php
  vendor/
  src/
  storage/

Then open:
- /diagnostico.php  (checks extensions + permissions)

If 'pdo_sqlite' or 'sqlite3' shows FALTA, this host can't run the project (SQLite required).
