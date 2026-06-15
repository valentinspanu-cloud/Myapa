#!/bin/bash
FOLDER=/var/www/myapa/storage/app/invoices

echo "Creare subdirectoare 2026..."
for luna in 01 02 03 04 05 06; do
    mkdir -p "$FOLDER/2026/$luna"
done

echo "Mutare fișiere 2026..."
for luna in 01 02 03 04 05 06; do
    count=$(find "$FOLDER" -maxdepth 1 -name "*_??${luna}2026.pdf" | wc -l)
    echo "  Luna $luna/2026: $count fișiere"
    find "$FOLDER" -maxdepth 1 -name "*_??${luna}2026.pdf" -exec mv {} "$FOLDER/2026/$luna/" \;
done

echo "Verificare:"
for luna in 01 02 03 04 05 06; do
    count=$(find "$FOLDER/2026/$luna" -name "*.pdf" | wc -l)
    echo "  2026/$luna: $count fișiere"
done

echo "Ramas in root:"
find "$FOLDER" -maxdepth 1 -name "*.pdf" | wc -l
echo "GATA"
