# MonRDV

Application SaaS de prise de rendez-vous médicaux multi-tenant à destination des
cliniques. Chaque clinique dispose de son propre espace isolé, avec gestion des
médecins, patients, rendez-vous, consultations, assurances et facturation.

## Stack

- **PHP 8.3** / **Laravel 13**
- **MySQL 8.4** (prod) / **SQLite `:memory:`** (tests)
- **Tailwind CSS 3** / **Alpine.js**
- **Docker + Laravel Sail** pour le dev local
- **Vite** pour le bundling frontend

## Architecture

- **Multi-tenancy** par `clinic_id` propagé via le trait `BelongsToClinic`
  (auto-fill à la création) + un `ClinicScope` global qui filtre
  automatiquement toutes les requêtes Eloquent selon la clinique de
  l'utilisateur authentifié.
- **Rôles** : `super_admin` (transversal), `admin`, `secretaire`, `medecin`,
  `patient`. Les `super_admin` contournent le `ClinicScope` ; tous les autres
  sont cloisonnés à leur clinique.
- **Policies Laravel** actives sur `Patient`, `RendezVous`, `Consultation`,
  `DocumentPatient` — voir `tests/Feature/PolicyTest.php`.
- **Audit RGPD** : modèle `ActivityLog` enregistre création / modification /
  suppression des données médicales + actions super admin + connexions /
  déconnexions / échecs de login (avec IP).

## Démarrer en local

Pré-requis : Docker Desktop + Git.

```bash
git clone https://github.com/nguimbifilience-web/monrdv.git
cd monrdv
git checkout feature/multi-tenancy

cp .env.example .env
# Adapter les valeurs DB_* si besoin (Sail utilise le service mysql par défaut)

./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
```

L'application est disponible sur http://localhost.

## Commandes utiles

```bash
# Tests (SQLite :memory:)
./vendor/bin/sail artisan test

# Lancer un sous-ensemble
./vendor/bin/sail artisan test --filter=PolicyTest

# Envoyer les rappels de RDV (à brancher sur un cron)
./vendor/bin/sail artisan rdv:rappels

# Consulter les logs applicatifs
./vendor/bin/sail artisan pail
```

## Comptes de démo après seed

- **Super Admin** : `superadmin@monrdv.ga`
- **Admin clinique** : voir `database/seeders/`

## Déploiement

La prod actuelle tourne sur InfinityFree (`https://mnrdv.42web.io`). Points
d'attention :

- `APP_ENV=production` / `APP_DEBUG=false` obligatoirement
- `FORCE_HTTPS=true` derrière le reverse proxy
- `SESSION_DRIVER=file`, `CACHE_STORE=file` (pas de Redis sur l'hébergement
  mutualisé)
- Logs visibles via FTP dans `storage/logs/laravel.log`

## Branches

- `main` : version stable d'avant la refonte multi-tenancy, conservée comme
  filet de sécurité.
- `feature/multi-tenancy` : version active avec Super Admin, multi-tenancy,
  audit RGPD, policies.

## Organisation du code

```
app/
  Http/Controllers/        → controllers métier + namespace SuperAdmin/
  Http/Middleware/         → AdminMiddleware, SuperAdminMiddleware, EnsureUserBelongsToClinic, RoleMiddleware
  Models/                  → Eloquent models + Scopes/ + Traits/BelongsToClinic
  Policies/                → PatientPolicy, RendezVousPolicy, ConsultationPolicy, DocumentPatientPolicy
database/
  migrations/              → schéma complet, multi-tenancy + index composites
  seeders/                 → comptes démo
resources/views/           → Blade + Tailwind
tests/Feature/             → tests HTTP + policies + multi-tenancy
```

## Rapport de suivi

Les décisions et corrections majeures sont loguées dans `1774534605_rapport.md`.
