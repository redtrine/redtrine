Cache
=====

Cache structure makes it easy to use `Redis` as a cache backend.

You can set an **optional** expiration time in seconds.

This is an example on how to cache some database records for 1 minute.

.. code-block:: php

    use Redtrine\Structure\Cache;

    // ...
    $someDatabaseRecords = ...

    $databaseCache = new Cache('cachedRecords');
    $databaseCache->set(json_encode($someDatabaseRecords), 60);

    // and to retrieve cached data in another HTTP Request
    $databaseCache = new Cache('cachedRecords');
    $cachedRecords = $databaseCache->get();
