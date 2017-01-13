<?php

namespace TODOListApi\Domain;

use Doctrine\ORM\EntityManager;
use TODOListApi\DTO\TodoItemCreationDTO;
use TODOListApi\DTO\TodoItemUpdateDTO;

class TodolistManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string[]
     */
    private $availableReporters;

    /**
     * @param EntityManager $entityManager
     * @param string[]      $availableReporters
     */
    public function __construct(EntityManager $entityManager, $availableReporters)
    {
        $this->entityManager = $entityManager;
        $this->availableReporters = $availableReporters;
    }

    /**
     * @param TodoItemCreationDTO $dto
     *
     * @return TodoItem
     */
    public function create(TodoItemCreationDTO $dto)
    {
        if (null !== $dto->dueAt) {
            $dueAt = new \DateTime($dto->dueAt);
        } else {
            $dueAt = null;
        }

        $item = new TodoItem(
            $dto->title,
            $dto->description,
            $dueAt,
            $dto->reporter,
            $dto->complexity,
            $dto->category,
            TodoItem::STATUS_TODO
        );

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    /**
     * @param TodoItem          $item
     * @param TodoItemUpdateDTO $dto
     *
     * @return TodoItem
     */
    public function update(TodoItem $item, TodoItemUpdateDTO $dto)
    {
        if (null !== $dto->dueAt) {
            $dueAt = new \DateTime($dto->dueAt);
        } else {
            $dueAt = null;
        }

        $item->update(
            $dto->title,
            $dto->description,
            $dueAt,
            $dto->complexity,
            $dto->category
        );

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    /**
     * @param int $itemId
     *
     * @return bool
     */
    public function startProgress($itemId)
    {
        /** @var TodoItem $item */
        $item = $this->entityManager->getRepository('TODOListApi\Domain\TodoItem')->findOneBy(['id' => $itemId]);

        if (null === $item) {
            throw new \Exception("Could not find item with id $itemId");
        }

        $item->startProgress();

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $itemId
     *
     * @return bool
     */
    public function complete($itemId)
    {
        /** @var TodoItem $item */
        $item = $this->entityManager->getRepository('TODOListApi\Domain\TodoItem')->findOneBy(['id' => $itemId]);

        if (null === $item) {
            throw new \Exception("Could not find item with id $itemId");
        }

        $item->complete();

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $itemId
     *
     * @return bool
     */
    public function resetStatus($itemId)
    {
        /** @var TodoItem $item */
        $item = $this->entityManager->getRepository('TODOListApi\Domain\TodoItem')->findOneBy(['id' => $itemId]);

        if (null === $item) {
            throw new \Exception("Could not find item with id $itemId");
        }

        $item->resetStatus();

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $itemId
     *
     * @return bool
     */
    public function delete($itemId)
    {
        /** @var TodoItem $item */
        $item = $this->entityManager->getRepository('TODOListApi\Domain\TodoItem')->findOneBy(['id' => $itemId]);

        if (null === $item) {
            throw new \Exception("Could not find item with id $itemId");
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return string[]
     */
    public function getAvailableReporters()
    {
        return $this->availableReporters;
    }
}
