# SYNOPSIS 
un highlight est une aggregation de plusieurs contenus de types mixtes. 
il est théoriquement possible d'ajouter à cette collection n'importe quel contenu par sa classe et son identifiant.

# Définitions des concepts et contenus.

## HIGHLIGHT 
Le contenu de type highlight porte les informations suivantes :

* un nom. optionnel mais nécessaire si non lié à un autre contenu.
* une référence vers un autre contenu par son type et son id. pour lier un highlight à un contenu. optionnel. (peut être voir à lié à plusieurs contenus) 
* une collection d'objets ordonnée qui représentent les contenus à remonter. REQUIS. appelons chacun de ces objets un _item_ et définissons les à l'instant

## ITEM 
un objet de type item porte les informations suivantes : 

* une référence vers un contenu de type mixte. c'est ce contenu qui sera remonté
* Une image, optionnelle, qui remplacera la couverture du contenu.
* une paragraphe, optionnel, qui remplacera le résumé par défaut de l'article.
* un lien, optionnel, qui remplacera le permalink du contenu par défaut.

# IMPLEMENTATION
