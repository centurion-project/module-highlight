*This module is not documented enough. This should be done soon!*

# SYNOPSIS 
An highlight content is an aggregation of many mixed types contents.
Theoretically, it is possible to add to this collection any content by its type and id.

# Concepts and contents

## HIGHLIGHT 
A `highlight` content carries the following information:

* a name. optional but necessary if the highlight can't be found by the content it is attached to.
* a reference to a content by its type and id. It is possible to find a highlight content from the content it is attached to.
* a ordered collection of objects representing the contents to bring up. Required. we'll call these objects _items._ These are defined just below

## ITEM 
An `item` content carries these informations:

* a reference to a mixed typed content. This is the content that will be displayed.
* an image. optional. Will be used as cover in place of the default one of the linked content.
* a paragraph. Optional. Will be used as introduction in place of the default one of the linked content.
* a link. optional. Will be used as replacement for the permalink of the linked content.

## CRAWLER
A `crawler` is an object that, given a specific query, can return a list of contents that match this query.
This module provides an abstract crawler. that has close to no crawling logic. it could have been an interface.
it also provides a default crawler. That is configurable and can crawler through tables with keywords.
have a look at `Highlight_Model_Crawler_Abstract` and `Highlight_Model_Crawler_Default` as well as the module config 

## FIELD MAPPER
Because the contents returned in a highlight can be of such different structure. it is necessary, when you need
to display them, to have simple tools to unify these structures a little.
A field mapper is an object that, given a row, can return an array with, at least, the following fields

* title: The title of the highlight item
* description: a short paragraph about the highlight item
* url: A link to the highlighted content
* cover: an image to go with the highlighted content

This module provides an Interface and a default mapper that read which fields to read from in a given order.
have a look at the module config files and the field mapper classes.
Of course, if the `item` itself defines any of the fields described above, those will see themselves overriden

# IMPLEMENTATION

Here follows a little guide on how to add highlights to your project

These very simple things will allow you to make lists of contents attached to a name you define in configuration
or a content of any type

## Named highlights

### definition

The concept of named highlights is very simple. Somewhere in your config files, you make a list of
highlights that you will be able to retrieve from your view in order to display them.

Here's how you go about that: create or open the config file in application/configs/highlight.ini

```ini

highlight.named_highlights[] = "home_carousel"
highlight.named_highlights[] = "home_footer"

```

In this example. We created two named highlights that were added to the highlight.named_highlights config array.

### management

How do we edit the content that will show up in these highlights?
go to the following url on your project: `/highlight/admin-highlight`.

You will be presented with and interface with one or more blue squares. each of these represents an `item`.
The last one is an empty one, ready to be added.

You can add one by clicking on the 'edit' action button.
A form pops up at the top of the page with a text field. If you type in it, you should see some smart autocomplete pop
just under it. The default crawler looks for flatpages only. but that should do fine for now.


Click add, and your item is added and is linked to the flatpage you selected !

If you repeat the operation you'll find yourself with a bunch of highlight items. you can drap and drop them to reorder
them. Don't forget to click "save order" at the bottom of the page.

