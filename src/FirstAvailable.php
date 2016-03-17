<?php
namespace PhillipsData\PrioritySchedule;

use SplQueue;
use CallbackFilterIterator;
use PhillipsData\PrioritySchedule\Exceptions\NoSuchElementException;

/**
 * First Available Priorirty Schedule implemented using a Queue
 */
class FirstAvailable implements ScheduleInterface
{
    /**
     * @var CallbackFilterIterator using an SplQueue
     */
    protected $filterQueue;

    /**
     * Initialize the priority schedule
     */
    public function __construct()
    {
        $this->filterQueue = new CallbackFilterIterator(
            new SplQueue(),
            function ($item) {
                return (bool) $item;
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * $callback Should accept a single parameter and return a bool
     * (true if valid, false otherwise)
     */
    public function setCallback(callable $callback)
    {
        $this->filterQueue = new CallbackFilterIterator(
            $this->filterQueue->getInnerIterator(),
            $callback
        );
    }

    /**
     * {@inheritdoc}
     */
    public function insert($item)
    {
        $this->filterQueue->getInnerIterator()->enqueue($item);
        // Must rewind the CallbackFilterIterator so it's primed to read from
        // the head of the SplQueue
        $this->filterQueue->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function extract()
    {
        $current = $this->current();
        $this->next();
        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->filterQueue->getInnerIterator()->count();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NoSuchElementException
     */
    public function current()
    {
        if (!$this->valid()) {
            throw new NoSuchElementException(
                'Can not extract from empty queue.'
            );
        }
        return $this->filterQueue->current();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->filterQueue->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->filterQueue->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->filterQueue->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->filterQueue->valid();
    }
}
