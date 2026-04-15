# Deploiement Vercel (Laravel + Inertia + Supabase)

Ce projet peut tourner en local et sur Vercel.

## 1) Prerequis Vercel

- Le repo contient deja:
  - `vercel.json` avec runtime PHP (`vercel-php`)
  - `api/index.php` qui delegue vers `public/index.php`

## 2) Variables d'environnement a definir sur Vercel

Configurer ces variables dans **Project Settings > Environment Variables**:

- `APP_KEY` (obligatoire)
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://organizz-p8fh.vercel.app` (ou ton domaine final)
- `LOG_CHANNEL=stderr`
- `SESSION_DRIVER=cookie`
- `SESSION_SECURE_COOKIE=true`
- `CACHE_STORE=array`
- `QUEUE_CONNECTION=sync`

### Base Supabase

- `DB_CONNECTION=supabase`
- `SUPABASE_DB_URL=postgresql://postgres:[MOT_DE_PASSE]@db.[PROJECT_REF].supabase.co:5432/postgres?sslmode=require`

Alternative: renseigner `SUPABASE_DB_HOST`, `SUPABASE_DB_PORT`, `SUPABASE_DB_DATABASE`, `SUPABASE_DB_USERNAME`, `SUPABASE_DB_PASSWORD`.

## 3) Migrations

Vercel n'est pas le bon endroit pour executer les migrations a chaque requete. Faire les migrations depuis un poste de dev/cicd vers Supabase:

```bash
php artisan migrate --force
```

## 4) Verification rapide apres deploy

- Ouvrir `/` puis connexion.
- Verifier qu'aucun `index.php` n'est telecharge.
- Verifier qu'une page Inertia charge bien le JS (`/build/assets/...`).

## 5) Local (inchangé)

```bash
php artisan serve
npm run dev
```

Le fonctionnement local reste identique.
