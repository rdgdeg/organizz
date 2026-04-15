# Deploiement Vercel (Laravel + Inertia + Supabase)

Ce projet peut tourner en local et sur Vercel.

## 1) Prerequis Vercel

- Le repo contient deja:
  - `vercel.json` avec runtime PHP (`vercel-php` via `functions`, pas seulement des fichiers statiques)
  - `api/index.php` qui delegue vers `public/index.php`

### Si le navigateur affiche ou telecharge le code PHP (`public/index.php`)

Vercel sert alors le dossier `public` comme **site statique** : les fichiers `.php` ne sont pas executes.

1. Ouvre **Project Settings → General → Build & Development Settings**.
2. **Output Directory** : laisse le champ **vide** (supprime `public` s’il est renseigne). Le deploiement doit inclure tout le projet + la fonction serverless `api/index.php`, pas uniquement le contenu de `public`.
3. **Framework Preset** : `Other` (ou rien qui force un export statique vers `public`).
4. **Root Directory** : racine du repo (ou le dossier ou se trouvent `vercel.json` et `composer.json`).
5. Redeploie (**Deployments → … → Redeploy**).

Sans ca, l’URL `/` peut servir `public/index.php` comme fichier brut au lieu de passer par `api/index.php` (runtime PHP).

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

### Base Supabase (obligatoire en production serverless)

SQLite ne fonctionne pas sur Vercel (pas de fichier persistant). Définir au minimum :

- `SUPABASE_DB_URL=postgresql://postgres:[MOT_DE_PASSE]@db.[PROJECT_REF].supabase.co:5432/postgres?sslmode=require`

Sans `DB_CONNECTION`, l’application choisit automatiquement :

- la connexion **`supabase`** si `SUPABASE_DB_URL` est défini ;
- sinon **`pgsql`** si une URL PostgreSQL est présente (`DB_URL`, `DATABASE_URL`, `POSTGRES_URL` ou `POSTGRES_PRISMA_URL`, typique avec l’extension Postgres Vercel) ;
- sinon **`sqlite`** (inadapté à Vercel : définir au moins une des URLs ci-dessus).

**Important :** ne pas s’appuyer sur la variable système `VERCEL` seule : sans URL de base, il faut absolument ajouter `SUPABASE_DB_URL` ou `DATABASE_URL` dans les variables du projet.

Alternative : `DB_CONNECTION=supabase` et renseigner `SUPABASE_DB_HOST`, `SUPABASE_DB_PORT`, `SUPABASE_DB_DATABASE`, `SUPABASE_DB_USERNAME`, `SUPABASE_DB_PASSWORD`.

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
