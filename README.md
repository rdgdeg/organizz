# Organizz — gestion de créneaux bénévoles

Dépôt GitHub : [github.com/rdgdeg/organizz](https://github.com/rdgdeg/organizz)

Application web (Laravel + Vue/Inertia + pages publiques Blade) pour organiser des postes et créneaux horaires, avec inscription publique pensée **smartphone** (lien partageable par SMS / messageries, page tactile, manifest « Ajouter à l’écran d’accueil ») et rappels par e-mail.

## Prérequis

- PHP 8.2+ avec extensions : `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Composer 2.x
- Node.js 20+ et npm (build des assets front)
- MySQL 8 (production) ou SQLite (développement local)

## Installation locale

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

Compte démo après `php artisan migrate:fresh --seed` :

- Email : `organizer@example.com`
- Mot de passe : `password`
- Page publique : `/event/week-end-solidaire-demo`

## Variables d’environnement importantes

| Variable | Rôle |
|----------|------|
| `APP_NAME`, `APP_URL` | Nom et URL canonique (HTTPS en production) |
| `APP_KEY` | Clé d’application (`php artisan key:generate`) |
| `DB_*` | Connexion SQLite / MySQL / ou `DB_CONNECTION=supabase` + `SUPABASE_DB_*` |
| `QUEUE_CONNECTION` | Utiliser `database` puis lancer un worker (`php artisan queue:work`) |
| `MAIL_*` | SMTP Planethoster ou autre (Brevo, etc.) |
| `SESSION_DRIVER` | `database` recommandé si plusieurs workers PHP |
| `SUPABASE_URL`, `SUPABASE_ANON_KEY` | Optionnel — API Supabase (voir ci-dessous) ; même URL / clé **anon** dupliquées en `VITE_*` si vous branchez un client JavaScript |
| `VITE_SUPABASE_URL`, `VITE_SUPABASE_ANON_KEY` | Exposées au build Vite — **uniquement la clé publique (anon)** |

Les files d’attente utilisent la table `jobs` (migration incluse).

### Supabase (PostgreSQL)

L’app peut utiliser **Supabase** comme base PostgreSQL : dans le dashboard (**Settings → Database**), récupérez le mot de passe et l’URI de connexion (**mode direct**, port **5432**, hôte `db.<project_ref>.supabase.co`).

1. Installez l’extension PHP **pdo_pgsql** (sinon `could not find driver`).
2. Dans `.env` : `DB_CONNECTION=supabase` et `SUPABASE_DB_URL="postgresql://postgres:[MOT_DE_PASSE]@db.[REF].supabase.co:5432/postgres?sslmode=require"` (voir `.env.example`).
3. Créez les tables : `php artisan migrate --force`

Les migrations Laravel créent `users`, `sessions`, `cache`, `jobs`, `events`, `positions`, `slots`, `registrations`, `reminder_rules`, etc. Le développement local peut rester sur **SQLite** (`DB_CONNECTION=sqlite`).

Les clés **API** Supabase (`SUPABASE_URL`, `SUPABASE_ANON_KEY`) servent au client JS / auth ; la **clé anon** et l’**URL** projet sont dans **Settings → API**. **Ne commitez jamais** le mot de passe base ni la **service_role**.

- La clé **service_role** (souvent appelée « secrète ») ne doit **pas** être placée dans `VITE_*` ni envoyée au navigateur : réservez-la au backend, avec des règles RLS strictes côté Supabase.
- Si une clé a fuité (chat, ticket, dépôt public), **régénérez-la** dans le dashboard Supabase.

Côté PHP, lecture possible via `config('services.supabase.url')` et `config('services.supabase.anon_key')`.

## Déploiement Planethoster (résumé)

1. **Uploader** le code (FTP/Git) hors dossiers `node_modules`, `.env` local.
2. Sur le serveur : `composer install --no-dev --optimize-autoloader`.
3. Configurer `.env` (MySQL, `APP_URL` en https, SMTP, `QUEUE_CONNECTION=database`).
4. `php artisan migrate --force`
5. `php artisan storage:link`
6. `npm ci && npm run build` (ou builder en CI et uploader `public/build`).
7. **Cron** (cPanel → Cron Jobs), toutes les minutes :

   ```cron
   * * * * * cd /chemin/vers/projet && php artisan schedule:run >> /dev/null 2>&1
   ```

   Le scheduler exécute notamment `events:send-reminders` chaque minute (l’heure d’envoi des rappels est filtrée dans la commande).

8. **File d’attente** : lancer un worker en continu si votre offre le permet (Supervisor, ou tâche cron dédiée) :

   ```bash
   php artisan queue:work database --sleep=3 --tries=3
   ```

9. **HTTPS** : forcer la redirection HTTPS dans le panneau ou via `.htaccess` / configuration du domaine.

## Limites plan gratuit (hooks V2)

Définies dans `config/plans.php` : événements actifs, postes par événement, inscriptions, règles de rappel, export CSV (réservé au plan Pro côté code — passer en Pro à finaliser en V2).

## Tests

```bash
php artisan test
```

Les tests désactivent Vite côté rendu pour éviter d’exiger un build dans l’environnement CI.

## Licence

Projet fourni tel quel pour usage du commanditaire (LD Media).
