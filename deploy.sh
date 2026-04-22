#!/bin/bash

# Script de préparation au déploiement pour AlertBook GBV (Railway)

echo "--- Préparation du déploiement ---"

# 1. Nettoyage et Optimisation Locale
echo "1. Nettoyage des caches locaux..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Build des Assets (Vite)
echo "2. Build des assets de production (NPM)..."
npm install
npm run build

# 3. Vérification des dépendances Composer
echo "3. Optimisation de l'autoloader Composer..."
composer install --no-dev --optimize-autoloader

echo ""
echo "--- Prêt pour le déploiement ---"
echo "Instructions Railway :"
echo "1. Assurez-vous d'avoir installé la Railway CLI : npm i -g @railway/cli"
echo "2. Connectez-vous : railway login"
echo "3. Liez le projet : railway link"
echo "4. Déployez : railway up"
echo "--------------------------------"
