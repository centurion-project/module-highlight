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

# IMPLEMENTATION

