# Laravel Record

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stechstudio/laravel-record.svg?style=flat-square)](https://packagist.org/packages/stechstudio/laravel-record)
[![Total Downloads](https://img.shields.io/packagist/dt/stechstudio/laravel-record.svg?style=flat-square)](https://packagist.org/packages/stechstudio/laravel-record)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

I'm going to assume you already know all about Laravel's awesome [Collection](https://laravel.com/docs/5.4/collections) class, 
and hopefully you've read [Refactoring to Collections](https://adamwathan.me/refactoring-to-collections/). 
(If you haven't, stop what you're doing and go buy that PDF. You'll thank me later.)
 
I also assume you know all about [Eloquent models](https://laravel.com/docs/5.4/eloquent). 

But have you ever wanted some of the functionality of a model, merged onto a collection? 

This is a super small, single-class library that brings those together just a bit.

# Benefits

Starting with the Collection class, I wanted to add:

1) **Magic getter for attributes**. If you have key/value pairs in your collection, the Collection class does provide
the [get](https://laravel.com/docs/5.4/collections#method-get) method. But I'm lazy. And I like accessing my collection
with plain 'ol object notation. You know, like a model. Record lets you do `$collection->attribute`.

2) **New collection for sub-arrays**. If you hand a multi-dimensional array to `collect()` and access a nested array, it's still
just an array. Like `$collection->get('attribute')['subattribute']`. I want collections all the way down! This will turn 
any sub-array into a new instance of Record, allowing you to do `$record->attribute->subattribute->as->deep->as->your->array->goes`. 
And because you still have a real collection at each level, you can use all of the goodies like `$record->attribute->subattribute->count()`.

3) **Custom accessors**. Just like Eloquent, you can extend the Record class and define a custom accessor. Create a
`getFooAttribute()` method and then just use `$collection->foo` to get your custom computed attribute.

# Quick example: handling rich arrays

I find myself frequently needing to handle a multi-dimensional array, often a response from a remote web service.
This array may have attributes (like 'name' or 'id') as well as a nested collections (like 'data' or 'rows'). 

Consider this:

```json
{
  "name" : "My Blog",
  "url" : "http://foo.dev",
  "posts" : [
    {
      "id" : 1,
      "title" : "Hello World",
      "content" : "...",
      "comments" : [
        {
          "name" : "John Doe",
          "email" : "john@example.com",
          "content": "..."
        }
      ]
    },
    {
      "id" : 2,
      "title" : "My second post",
      "content" : "...",
      "comments" : [
        ...
      ]
    }
  ]
}
```

We can take this whole payload and navigate it quite nicely with Record:

```php
$record = record(json_decode($webServiceResponse, true));

echo $record->name; // My Blog
echo $record->posts->count(); // 2
echo $record->posts->first()->title; // Hello World
echo $record->posts->first()->comments->count(); // 1
```

Nice! At each level I get a combination Laravel's Collection class, plus some attribute goodness borrowed from Model.

Furthermore I might extend Record and create a class with custom accessors to sanitize `content`, or split the `name` into
first and last, or... you get the idea.

# Installation

You know the drill.

```
composer require stechstudio/laravel-record
```

Then you can either:

```php
$record = new STS\Record\Record([...]);
```

Or you can use the `record` helper method:

```php
$record = record([...]);
```
