*Ce module n'est pas assez documenté, mais devrait l'être sou peu !*

*Si vous cherchez un rapide guide, vous devriez sauter directement vers le chapitre sur [L'implémentation](#implementation)*

# SYNOPSIS 
Un contenu Highlight est un agregat de plusieurs contenus de types mixtes.
En théorie, il est possible d'ajouter à cette collection n'importe quel model par sa clé primaire et son type

# Concepts and contents

## HIGHLIGHT 
Un contenu `highlight` porte les informations suivantes:

* __Un nom.__ Optionnel mais nécéssaire si le highlight ne peut être trouvé par le contenu auquel il est rattaché.
* __Une référence__ vers un contenu. Il est possible de trouver un highlight à partir de ce contenu.
* a ordered collection of objects representing the contents to bring up. Required. we'll call these objects _items._ These are defined just below
* __Une collection ordonnée__ d'objets représentant les contenus à faire remonter. Requis. Nous appellerons ces objets _items._  Ils sont définis ci-dessous


## ITEM 
An `item` content carries these informations:
Un contenu `item` contient les informations suivantes:

* __Une réference__ vers un contenu de type indéfini. C'est le contenu qui sera remonté.
* __Une Image__ optionnelle. Elle sera utilisée au lieu de l'image du contenu.
* __Un paragraphe__ Optionnel. Il sera affiché à la place de celui du contenu.
* __Un lien__ optionnel. Il sera utilisé à la place du permalink vers le contenu.

## CRAWLER
Un `crawler` est un object qui, donné une certaine requête, peut retourner une liste de contenus qui correspondent à cette requête.
Ce module fournit un crawler abstrait. Seul, il ne fait quasiment rien.
Est aussi fournit, un crawler par défaut qui est configrable et peut parcourir des tables avec des mots clés.
Jetez un œil à la classe `Highlight_Model_Crawler_Abstract` et `Highlight_Model_Crawler_Default` ainsi qu'à la configuration du module.

## FIELD MAPPER
Comme les contenus remontés dans un `highlight` peuvent avoir des structures très différentes, il est nécéssaire pour les afficher
de disposer d'outils pour unifier ces structures.
Un "Field Mapper" est un objet qui, depuis une `row`, peut construire un tableau comportant, au minimum, les champs suivants:

* __title:__ Le titre de l'item
* __description:__ un paragraphe court pour l'item.
* __link:__ une url vers le contenu remonté
* __cover:__ une image pour illustrer l'item

Ce module fournit une interface et un mapper par défaut qui essaye de lire tour à tour les champs de la `row` qu'il doit mapper
dans un certain ordre.
Jetez un œil à la configuration du module et les classe de Field Mapper.
Bien sûr un Item peut surcharger les champs retournés par le mapper.

# IMPLEMENTATION

Voici un petit guide pour ajouter des Highlights à votre projet.

En commençant par les choses les plus simples, nous allons voir comment lier une list de contenus à remonter à un nom
en configuration ou à un contenu arbitraire.

## highligts nommés

### définition

Le concept de highlights nommés est très simple. Quelque part dans vos fichiers de configuration, vous définissez un liste
de nom par lesquels vous serez capable de retrouver vos highlights pour les affichier dans vos pages.

Voici comment faire: créez ou ouvrez le fichier de configuration application/configs/highlight.ini

```ini

highlight.named_highlights[] = "home_carousel"
highlight.named_highlights[] = "home_footer"

```

Dans cet exemple, nous avons créé 2 highlights nommés en les ajoutant à la clé de configuration highlight.named_highlights.

### gestion

Comment éditer les contenus qui remonteront dans ces highlights ?
Rendez-vous à l'url suivante sur votre projet: `/highlight/admin-highlight`.

Vous y trouverez un interface avec un ou plusieurs rectangles bleux. Chacun représente un `item`.
Le dernier est vide et prêt a être ajouté.

Vous pouvez l'ajouter en cliquant sur le bouton d'action "edit" (icône de petit crayon).
Un formulaire apparaît en haut de page avec un champs de texte. En tapant dedans, une liste de complétion apparait.
Le crawler par défaut cherches des flatpages. Cela devrait suffire pour le moment.

Cliquez sur ajouter, et votre item sera créé déjà lié à votre flatpage.

En répétant l'opération plusieurs fois, vous aurez une liste fournie d'items. Vous pouvez les réorganiser par glisser-déposer.
Ne pas oublier de cliquer le buton "sauvegarder l'ordre" en bas de page.

### Afficher des highlights

En allant toujours au plus simple, regardons comment afficher un highlight dans un script de vue

```php

<?php
    $container = $this->getHighlightContainer('home_footer');
?>
<?php if($container) : ?>
    <ul>
    <?php foreach($container->getHighlights() as $highlight) : ?>
        <li>
            <h3><?php echo $highlight['title'] ?></h3>
            <p><?php echo $highlight['description'] ?></p>
            <a href="<?php echo $highlight['link'] ?>">
                <img src="<?php echo $highlight['cover']->getStaticUrl() ?>" />
            </a>
        </li>
    <?php endforeach ?>
    </ul>
<?php endif ?>

```

* tout d'abord, nous récupérons le conteneur de highlight par son nom en utilisant l'helper de vue `GetHighlightContainer`.
* après avoir vérifié que nous avons bien un conteneur, nous commençont à afficher une liste non ordonnée
* nous récuprons chaque item avec la méthode `getHighlight`.
* pour chacun, nous affichons un élément de liste à notre guise.

Le helper `GetHighlightContainer` prends en paramètre un nom et récupère le highlight correspondant. Nous verrons plus
tard qu'il peut faire un peu plus que cela aussi.

La méthode `getHighlights` sur un conteneur retourne une collection de item déjà mappés. Nous l'avons utilisée sans paramètres
mais on peut aussi lui préciser le field mapper à utiliser. Si il n'est pas précisé, c'est celui par défaut qui est utilisé.


## Highlights pour un contenu en particulier

Tout ceci est déjà intéressant. Mais si je veux, par exemple, choisir 3 articles qui remonterons dans la sidebar de la page
de mon article sur un voyage en Suisse, comment faire?
Je pourrais définir un highlight nommé pour ce cas en particulier bien sûr.

Mais si je veux un comportement similaire sur n'importe lequel de mes articles ? Et bien, ce module permet d'attacher
un highlight à un contenu. Voyons les étapes nécéssaires pour l'accomplir.

### Dire à l'interface d'aministration quel est le contenu courant

L'interface d'aministration des highlights vérifie plusieurs paramètres de l'URL pour savoir si nous voulons nous occuper
des highlights d'un contenu en particulier. Ces paramètres sont : 

* `proxy_pk`: La clé primaire du contenu à lier.
* `proxy_content_type_id`: L'identifiant du type de contenu à lier.

Bien sûr nous pourrions écrire ces paramètres à la main. Mais je préfère personnellement quelque chose pour les générer
à ma place !

Ouvrons le controller CRUD de votre type de contenu favoris. j'aime les profils utilisateurs

```php

<?php

class User_AdminProfileController extends Centurion_Controller_CRUD
                                  implements Highlight_Traits_Controller_CRUD_Interface
{
    public function init()
    {

        // ... nothing left to change actually
 

```


Qu'avons nous changé ? Le controller Implémente l'interface `Highlight_Traits_Controller_CRUD_Interface`. C'est de cette
manière que Centurion gère le mécanisme de traits.

Que fait ce trait ? Il ajoute, dans une liste de contenus, une colonne avec un lien pour gérer les contenus de ce contenu .

### Récupérer les highlight d'un contenu

C'est en réalité très simple. On utilise le même helper de vue que précédemment, sauf qu'au lieu de lui donner un nom
en paramètre, nous lui donnons l'objet row que nous avons.

## Surcharger les comportements par défaut

Jusqu'à maintenant, nous avons utilisé les comportements par défaut de nombreux composants. Ce qui nous a permis
d'écrire un minimum de code et de configuration

Mais ce module fournit aussi des moyens d'étendre les comportements par défaut de ses différents composants.

### Crawlers personnalisés

La première chose que vous aurez besoin de surcharger est probablement le crawler. Il est difficile de définir
un comportement générique puisqu'on ne peut pas connaître les objets métier de chaque projet à l'avance.

#### Dire à l'interface d'aministration quel crawler utiliser

There are different ways to tell the admin highlight controller which crawler to use. If it fails finding any, it will
use the default one.
Il y a plusieurs manières de définir quel crawler doit être utilisé par l'interface d'aministration. Si aucune ne donne de résultat,
le crawler par défaut sera utilisé.

First of all, you can declare any crawler you like in the configuration in the namespace `highlight.crawlers.*`.
Avant toute chose, vous pouvez déclarer des crawlers dans les fichiers de configuration dans le namespace `highlight.crawlers.*`.
La Crawler Factory (`Highlight_Model_Crawler_Factory::get`) prend en paramètre le nom d'un crawler. D'après ce nom 'crawlername', elle lit
La configuration dans `highlight.crawlers.crawlername`. Elle instancie un objet de la classe définie dans
`highlight.crawlers.crawlername.className`, en envoyant en paramètre le reste de la configuration pour cette clé.


###### paramètre d'URL

Si on ajoute un paramètre `crawler` à l'URL, ce nom sera envoyé à la Factory.
Pour voir comment surcharger l'url pour y mettre le paramètre de crawler, jetez un œil à `Highlight_Traits_Controller_CRUD`

###### dans la configuration:

Pour un highlight nommé, au lieu de le définir en tant que tableau

```ini

highlight.named_highlights[] = "home_carousel"

```

vous pouvez le déclarer avec un paramètre crawler comme ceci :

```ini

highlight.named_highlights.home_carousel.crawler = "my_funny_crawler"

```

###### Dans la row du contenu :

Lorsque l'on gère les highlight d'un contenu, si la row de ce modèle implémente l'interface
`Highlight_Traits_Model_Row_HasHighlights_Interface` it then has to implement a `getCrawler` method which returns
`Highlight_Traits_Model_Row_HasHighlights_Interface` et doit donc définir un méthode getCrawler qui retourne le crawler à
utiliser lorsqu'on doit ajouter des highlight items à cette row.


#### The hard way: implement the c

Let's start with the hard way, for a change.

The hard way is actually the simplest to explain. You'll have to implement your own crawler by extending the abstract
one provided. Let's see what's in there:

* a unique abstract method `crawl`: it takes an array as parameter because, you never really know what to expect.
Most the time it will be populated with a unique key `query` with a string. It expected in return, an array of rows
* an `autocomplete` method: You probably don't need to override that one. it only formats the result of `crawl`
for the highlight controller
* a `crawlTable` method: This method actually does something. It splits the query string into keywords, and crawls
a table for the given fields matching these keywords

Obviously, you'll have to start with the `crawl` method. And since you're in your IDE, you probably should have a
look at the source code.


#### The simle way: configure the default crawler

What if I told you, you don't need to write code? You could just configure a crawler, very much like the default one
which would result in the instanciation of the same class but with different parameters.

The default crawlers reads from its parameters which tables it has to crawl and which fields for each of these tables.
pretty straightforward. Have a look at the module.ini file to see how the default one is configured.


```ini

; the class to instanciate
highlight.crawlers.default.className = "Highlight_Model_Crawler_Default";

; each key of the config defines a model it has to crawl
; the table config define which table it has to crawl
; the fields config array lists the fields in which to look for matches with the query terms
highlight.crawlers.default.models.profile.table = "user/profile";
highlight.crawlers.default.models.profile.fields[] = "nickname"
highlight.crawlers.default.models.profile.fields[] = "user__email"
highlight.crawlers.default.models.flatpages.table = "cms/flatpage";
highlight.crawlers.default.models.flatpages.fields[] = "title";
highlight.crawlers.default.models.flatpages.fields[] = "url";
highlight.crawlers.default.models.flatpages.fields[] = "slug";

```

### Custom field mappers

As said before, field mappers are little utility objects whose role is to format any single content into a unified structure of data.
Pretty much any method that needs a field mapper as argument will take either a FieldMapper object or a string for retrieval
from the factory.

_TODO: a wiki page about the crawler component alone_

#### Mapper factory

Very much like the Crawler factory, the field mapper factory reads a Mapper's configuration from configuration in
the `highlight.mappers.*` namespace.

#### Implement the field mapper interface

The interface defines two methods.

* `map(Centurion_Db_Table_Row_Abstract $row)` takes a row of any kind and must return an associative array with the necessary keys for a highlight item
* `mapRowSet($rowset)` takes a collection of rows (can also be an array) and must return an array of the same size with each entry mapped

When implementing this, make sure you take into account the case where the row given to the map function is an instance
of `Highlight_Model_DbTable_Row_Row` that is a Highlight entry.

#### Configure the default mapper class

The behaviour of the default field mapper class is for each field it has to find a value for, try a list of field in the
given row and take the first one that exist and is not empty.

The best way to understand it is to look at the config file

```ini

; config for the default field mapper
; the class to instanciate
highlight.mappers.default.className = "Highlight_Model_FieldMapper_Default"
; each key within the mapper's config describe the components of the final field it names
; the fields array lists the fields of a row the mappers will look in to find the content of the final field
highlight.mappers.default.title.fields[] = "title"
highlight.mappers.default.title.fields[] = "name"
highlight.mappers.default.link.fields[] = "permalink"
highlight.mappers.default.link.fields[] = "url"
highlight.mappers.default.description.fields[] = "abstract"
highlight.mappers.default.description.fields[] = "intro"
highlight.mappers.default.description.fields[] = "introduction"
highlight.mappers.default.description.fields[] = "introduction"
highlight.mappers.default.description.fields[] = "body"
highlight.mappers.default.description.fields[] = "description"
highlight.mappers.default.cover.fields[] = "cover"
highlight.mappers.default.cover.fields[] = "image"
highlight.mappers.default.cover.fields[] = "media"

; pixelOnEmpty allows to populate the value with the empty pixel if nothing is set
highlight.mappers.default.cover.pixelOnEmpty = 1


```

As you can see, for each field, we build a list of fields to check to find a value.

