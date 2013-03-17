KeyPair
=======

The `KeyPair` is a structure where unique values are assigned an ID.

You can think about this as a table with a primary auto-increment key and a single unique column.

Internally, the `KeyPair` uses two Redis hashes to provide O(1) lookup by both ID and value.
It also uses a Redis key to store auto-increment counter.

Redis Structure:
    *    `(namespace:)key     = hash(id => value)`
    *    `(namespace:)key:ids = hash(value => id)`
    *    `(namespace:)key:autoinc = integer`

.. code-block:: php

    use Redtrine\Structure\KeyPair;

    $redisTable = new KeyPair('redisTable');
    $redisTable->add('a');
    $redisTable->add('b');
    $redisTable->add('c');

    // At this moment we have an structure like {"1":"a", "2":"b", "3":"c"}
    // And we can perform queries by id, value, delete by id and value, etc...

    $values = $redisTable->getById(array(1, 2)); // $values is array('a', 'b')

    $id = $redisTable->get('a'); // $id is 1

    // These 2 expressions would be equivalent in terms of structure
    $redisTable->delete('b');
    $redisTable->deleteById(2);



