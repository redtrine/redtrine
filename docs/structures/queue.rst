Queue
=====

Queue structure makes it easy to use `Redis` as a queue.

Queues can be defined as **FIFO** or **LIFO** via a constructor argument.

You can add elements to the `Queue` with the `enqueue` method and extract elements
from it with the `dequeue` method.

You can also send some elements to another Queues. This can be useful for instance
if some records need to be processed with a different priority.

This is an example about how to send data to a Redis queue and consume it somewhere else

.. code-block:: php

    use Redtrine\Structure\Queue;

    // ...
    $processData = // ... some data to start a process

    $processQueue = new Queue('process');
    $processQueue->enqueue($processData);


    // ... somewhere else in the code there should be a Queue consumer

    $processQueue = new Queue('process');
    $processData = $processQueue->dequeue();

    // ... run a process with $processData

And this is an example about how to send data from one `Queue` to another

.. code-block:: php

    use Redtrine\Structure\Queue;

    // ...
    $processData = // ... some data to start a process

    $processQueue = new Queue('process');
    $processQueue->enqueue($processData);

    // And we decide to move some queued data to a higher priority queue
    $betterQueue = new Queue('highpriority');
    $processQueue->dequeueEnqueue($betterQueue);
