<?php

namespace TODOListApi\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use TODOListApi\Domain\TodoItem;
use TODOListApi\Domain\TodolistManager;
use TODOListApi\Domain\TodolistRepository;
use TODOListApi\DTO\TodoItemCreationDTO;
use TODOListApi\DTO\TodoItemUpdateDTO;

class TodolistController extends BaseController
{
    /**
     * @var EntityManager
     */
    private $doctrine;

    /**
     * @var TodolistManager
     */
    private $todoListManager;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param EntityManager   $doctrine
     * @param TodolistManager $todoListManager
     * @param Serializer      $serializer
     */
    public function __construct(
        EntityManager $doctrine,
        TodolistManager $todoListManager,
        Serializer $serializer)
    {
        $this->doctrine = $doctrine;
        $this->todoListManager = $todoListManager;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $dto = $this->buildCreationDTO($request->request);

        $errors = $dto->isValid();
        if (true !== $errors) {
            return $this->buildBadRequestResponse($errors);
        }

        $item = $this->todoListManager->create($dto);

        return $this->buildSuccessfulResponse(['id' => $item->getId()]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        $bag = $request->attributes;
        $itemId = $bag->get('id');

        if (null === $itemId) {
            return $this->buildBadRequestResponse(['id' => 'id is mandatory']);
        }

        $item = $this->getTodoItemById($itemId);
        if (null === $item) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $itemId"]);
        }

        $dto = $this->buildUpdateDTO($request->request);

        $errors = $dto->isValid();
        if (true !== $errors) {
            return $this->buildBadRequestResponse($errors);
        }

        $item = $this->todoListManager->update($item, $dto);

        return $this->buildSuccessfulResponse(['id' => $item->getId()]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function startProgressAction(Request $request)
    {
        $bag = $request->attributes;
        $itemId = $bag->get('id');

        if (null === $itemId) {
            return $this->buildBadRequestResponse(['id' => 'id is mandatory']);
        }

        $item = $this->getTodoItemById($itemId);
        if (null === $item) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $itemId"]);
        }

        if (TodoItem::STATUS_TODO !== $item->getStatus()) {
            return $this->buildBadRequestResponse(['id' => "Only 'todo' items can start progress"]);
        }

        $result = $this->todoListManager->startProgress($itemId);

        if (true === $result) {
            return $this->buildSuccessfulResponse();
        } else {
            return $this->buildBadResponse();
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function completeAction(Request $request)
    {
        $bag = $request->attributes;
        $itemId = $bag->get('id');

        if (null === $itemId) {
            return $this->buildBadRequestResponse(['id' => 'id is mandatory']);
        }

        $pendingStatuses = [TodoItem::STATUS_TODO, TodoItem::STATUS_IN_PROGRESS];

        $item = $this->getTodoItemById($itemId);
        if (null === $item) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $itemId"]);
        }

        if (false === in_array($item->getStatus(), $pendingStatuses)) {
            return $this->buildBadRequestResponse(['id' => 'Only pending items can start progress']);
        }

        $result = $this->todoListManager->complete($itemId);

        if (true === $result) {
            return $this->buildSuccessfulResponse();
        } else {
            return $this->buildBadResponse();
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $itemId = $request->attributes->get('id');
        if (null === $itemId) {
            return $this->buildBadRequestResponse(['id' => 'id is mandatory']);
        }

        $item = $this->getTodoItemById($itemId);
        if (null === $item) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $itemId"]);
        }

        $result = $this->todoListManager->delete($itemId);

        if (true === $result) {
            return $this->buildSuccessfulResponse();
        } else {
            return $this->buildBadResponse();
        }
    }

    /**
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $repository = $this->getTodoItemRepository();
        $result = $repository->findAll();

        return $this->serializer->serialize($result, 'json');
    }

    /**
     * @return JsonResponse
     */
    public function getAction($id)
    {
        $item = $this->getTodoItemById($id);
        if (null === $item) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $id"]);
        }

        return $this->serializer->serialize($item, 'json');
    }

    /**
     * @return JsonResponse
     */
    public function getReportersAction()
    {
        $result = $this->todoListManager->getAvailableReporters();

        return $this->serializer->serialize($result, 'json');
    }

    /**
     * @param ParameterBag $bag
     *
     * @return TodoItemCreationDTO
     */
    private function buildCreationDTO(ParameterBag $bag)
    {
        $dto = new TodoItemCreationDTO();

        $dto->dueAt = $bag->get('dueAt');
        $dto->title = $bag->get('title');
        $dto->description = $bag->get('description');
        $dto->reporter = $bag->get('reporter');
        $dto->complexity = $bag->get('complexity');
        $dto->category = $bag->get('category');
        $dto->availableReporters = $this->todoListManager->getAvailableReporters();

        return $dto;
    }

    /**
     * @param ParameterBag $bag
     *
     * @return TodoItemUpdateDTO
     */
    private function buildUpdateDTO(ParameterBag $bag)
    {
        $dto = new TodoItemUpdateDTO();

        $dto->dueAt = $bag->get('dueAt');
        $dto->title = $bag->get('title');
        $dto->description = $bag->get('description');
        $dto->complexity = $bag->get('complexity');
        $dto->category = $bag->get('category');

        return $dto;
    }

    /**
     * @return TodolistRepository
     */
    private function getTodoItemRepository()
    {
        $repository = $this->doctrine->getRepository('TODOListApi\Domain\TodoItem');

        return $repository;
    }

    /**
     * @param int $itemId
     *
     * @return TodoItem|null
     */
    private function getTodoItemById($itemId)
    {
        $repository = $this->getTodoItemRepository();
        $items = $repository->findBy(['id' => $itemId]);
        if (empty($items)) {
            return null;
        }
        $item = current($items);

        return $item;
    }
}
