# Pimp your API! Pimp your models!

Does your API need to allow **filtering** results? **Of course it does**!

Do you need to **sort** the results or **return selected relations** together with your model? **I bet you do**!

**Let's do this!**

**Pimp your model!**

    class Post extends Eloquent {
      use PimpableTrait;
    }

**Pimp your API!**

    class PostsController extends Controller {
      public function index() {
        return Post::pimp()->get();
      }
    }

**What data do you need?**

> I need all the posts in a thread with ID 42...

> ...that contain a string "awesome"...

> ...that are no older than March 2014...

> ...that have been posted by any user other than the one with ID 666.

> One more thing... sort it so that I get active posts at the top...

> Oh, and make active and inactive posts sorted by the date they were posted...

> Did I mention I need the user data with every post?

**Ready... Steady... Go!**

    GET /api/posts
      ?thread_id=42
      &text=%awesome%
      &created_at=(ge)201403
      &user_id=!666
      &sort[]=is_active,desc
      &sort[]=created_at,desc
      &with=user

**BAM! Done! How cool is that!**

## Overview

Laravel 4/5 package that allows to dynamically filter, sort and eager load relations for your models using request parameters.

It combines the following packages:

- [Searchable](https://github.com/jedrzej/searchable) - filter models
- [Sortable](https://github.com/jedrzej/sortable) - sort models
- [Withable](https://github.com/jedrzej/withable) - eager load relations

It simplifies embedding them in your models and allows using all 3 of them with a single function call.

## Composer install

Add the following line to `composer.json` file in your project:

    "jedrzej/pimpable": "0.0.4"

or run the following in the commandline in your project's root folder:

    composer require "jedrzej/pimpable" "0.0.4"

## Usage

### Pimp your model
In order to pimp your model class, you need to import **PimpableTrait** into your model. This will internally import all 3 behaviours.

    class Model extends Eloquent {
      use PimpableTrait;
    }

By default all model fields are searchable and sortable; all relations can be eagerly loaded by default as well.
If you need to limit which fields can model be filtered and sorted by and which relations can be loaded, see documentation
of corresponding behaviour package.

### Pimp your API

Once you pimp your model, additional method **pimp()** will be available on the model that enables the additional behaviour.
All criteria will be taken from the request automatically, but if you want to override the request parameters, you can
pass the desired value to the **pimp()** method:

    //override all parameters
    return Model::pimp($filters, $sort, $relations)->get();

    //override sorting criteria only
    return Model::pimp(null, $sort)->get();

Information how to configure the behaviours using request parameters can be found in documentation of corresponding behaviour package.

### Additional configuration
 If you are using `sort` request parameter for other purpose, you can change the name of the parameter that will be
 interpreted as sorting criteria by setting a `$sortParameterName` property in your model, e.g.:

     protected $sortParameterName = 'sortBy';

 If you are using `with` request parameter for other purpose, you can change the name of the parameter that will be
  interpreted as a list of relations to load by setting a `$withParameterName` property in your model, e.g.:

     protected $withParameterName = 'relations';