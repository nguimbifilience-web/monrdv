# Deploy scripts

Scripts jetables de deploiement / maintenance pour la prod InfinityFree.

Ils sont **tous gitignores** (sauf ce README et `.deploy-token.example`). Le
token de protection n'est plus code en dur dans les scripts : il est lu depuis
un fichier externe `.deploy-token` qui accompagne les scripts sur le serveur.

## Process pour chaque deploiement

1. **Generer un nouveau token** :

   ```bash
   openssl rand -hex 32 > deploy/.deploy-token
   ```

2. **Uploader sur le serveur** :
   - `deploy/migrate.php` (ou `fix-cache.php` / `reset-password.php` selon besoin)
   - `deploy/.deploy-token` (au meme niveau que le script, donc `htdocs/.deploy-token`)

3. **Executer via navigateur** :

   ```
   https://mnrdv.page.gd/migrate.php?token=<contenu-de-.deploy-token>
   ```

4. **Nettoyer immediatement** :
   - Supprimer `htdocs/migrate.php` (ou autre script utilise)
   - Supprimer `htdocs/.deploy-token`

## Scripts disponibles

| Script | Role | Usage |
|--------|------|-------|
| `migrate.php` | Lance `php artisan migrate --force` + seeders de config + clear caches | Apres changement de schema |
| `fix-cache.php` | Purge les caches Laravel (bootstrap, views, data, sessions) | Quand "Target class does not exist" apparait |
| `reset-password.php` | Reinitialise le mot de passe d'un user (sauf super_admin) | Recuperation compte medecin/admin/secretaire |

## Archives zip

Les `.zip` contiennent les fichiers modifies pour chaque deploiement. Ils sont
gitignores (`.gitignore` couvre `/deploy/*.zip`).

Historique des zips deployes : voir `1774534605_rapport.md`.

## Note securite

- Les tokens ne doivent **jamais** etre committes.
- Le token doit etre **different a chaque deploiement**.
- Les scripts doivent etre **supprimes du serveur immediatement apres usage**.
- Si un token est compromis : regenerer + supprimer les anciennes references.
