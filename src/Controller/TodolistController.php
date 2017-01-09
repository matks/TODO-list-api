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
     * @param EntityManager $doctrine
     * @param TodolistManager $todoListManager
     * @param Serializer $serializer
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
        $dto = $this->buildDTO($request->request);

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
    public function startProgressAction(Request $request)
    {
        $bag = $request->attributes;
        $itemId = $bag->get('id');

        if (null === $itemId) {
            return $this->buildBadRequestResponse(['id' => 'id is mandatory']);
        }

        $repository = $this->getTodoItemRepository();
        $items = $repository->findBy(['id' => $itemId]);
        if (empty($items)) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $itemId"]);
        }
        $item = current($items);
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

        $repository = $this->getTodoItemRepository();
        $items = $repository->findBy(['id' => $itemId]);
        if (empty($items)) {
            return $this->buildBadRequestResponse(['id' => "Cannot find TODO item for id $itemId"]);
        }
        $item = current($items);
        if (false === in_array($item->getStatus(), $pendingStatuses)) {
            return $this->buildBadRequestResponse(['id' => "Only pending items can start progress"]);
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

        $repository = $this->getTodoItemRepository();
        $item = $repository->findBy(['id' => $itemId]);
        if (empty($item)) {
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
        $repository = $this->getTodoItemRepository();
        $result = $repository->findOneBy(['id' => $id]);

        return $this->serializer->serialize($result, 'json');
    }

    /**
     * @param ParameterBag $bag
     *
     * @return TodoItemCreationDTO
     */
    private function buildDTO(ParameterBag $bag)
    {
        $dto = new TodoItemCreationDTO();

        $dto->due_at = $bag->get('due_at');
        $dto->title = $bag->get('title');
        $dto->description = $bag->get('description');
        $dto->reporter = $bag->get('reporter');
        $dto->complexity = $bag->get('complexity');
        $dto->category = $bag->get('category');
        $dto->availableReporters = $this->todoListManager->getAvailableReporters();

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
}
