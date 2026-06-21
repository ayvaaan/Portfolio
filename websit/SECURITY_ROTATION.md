# Removing sensitive data and rotating secrets

If you committed credentials or secrets into this repository, follow these steps to rotate and scrub history.

1) Rotate credentials immediately
   - Change the database user password and any API keys or SMTP credentials that were committed.
   - Update your server configuration with the new credentials.

2) Add .env to .gitignore and move secrets to environment variables
   - echo "/websit/.env" >> .gitignore
   - Copy values to websit/.env (not committed)

3) Remove secrets from Git history (choose one of the following):

   Option A: Use BFG (simpler)
   - Install BFG: https://rtyley.github.io/bfg-repo-cleaner/
   - Run (replace 'PASSWORD' with the pattern or file containing secrets):
     bfg --delete-files your-file-with-secrets
     # or replace text
     bfg --replace-text replacements.txt
   - Follow with:
     git reflog expire --expire=now --all
     git gc --prune=now --aggressive
     git push --force

   Option B: Use git filter-repo (recommended)
   - Install: https://github.com/newren/git-filter-repo
   - Example to remove a file:
     git filter-repo --path websit/assets/php/config.php --invert-paths
   - Or to replace a secret string:
     git filter-repo --replace-text replacements.txt
   - After filtering, push with --force.

4) Inform collaborators and rotate any secrets that were exposed.

Important: Rewriting history will change commit hashes. Coordinate with collaborators and consider creating a fresh repository if necessary.

If you want, I can generate the exact replacements.txt and the sequence of commands tailored to this repo (including which files/strings to remove). Reply if you want me to prepare that and I will create the files/commands.
