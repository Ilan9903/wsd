#!/bin/bash

# --- CONFIG ---
CONTAINER_NAME="wesend-api"
 #----------------

GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

 #Vérifie que le conteneur est en cours d'exécution
if [ -z "$(docker ps -q -f name=${CONTAINER_NAME})" ]; then
    echo -e "${RED}❌ Le conteneur ${CONTAINER_NAME} n'est pas en cours d'exécution.${NC}"
    echo "Démarre-le avant de lancer le script (ex: docker compose up -d)"
    exit 1
fi

echo -e "${GREEN}🧹 Lancement de Laravel Pint dans ${CONTAINER_NAME}...${NC}"
docker exec ${CONTAINER_NAME} ./vendor/bin/pint --test
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Pint a détecté des problèmes de formatage.${NC}"
    echo "Corrige-les avec : docker exec ${CONTAINER_NAME} ./vendor/bin/pint"
    exit 1
fi

echo -e "${GREEN}🔍 Lancement de PHPStan dans ${CONTAINER_NAME}...${NC}"
docker exec ${CONTAINER_NAME} ./vendor/bin/phpstan analyse --memory-limit=512M
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ PHPStan a détecté des erreurs.${NC}"
    exit 1
fi

echo -e "${GREEN}🧪 Lancement de PHPUnit dans ${CONTAINER_NAME}...${NC}"
docker exec ${CONTAINER_NAME} php artisan test --coverage --min=70 --no-ansi

# Vérifie le code de sortie immédiatement après l'exécution de la commande
EXIT_CODE=$?
echo "Code de sortie de la commande PHPUnit : ${EXIT_CODE}"

if [ $EXIT_CODE -ne 0 ]; then
    echo -e "${RED}❌ Les tests PHPUnit ont échoué.${NC}"
    exit 1
fi


echo -e "${GREEN}✅ Tout est bon ! Commit autorisé.${NC}"
exit 0
