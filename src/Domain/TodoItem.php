<?php

namespace TODOListApi\Domain;

class TodoItem
{
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_DONE = 'done';

    const COMPLEXITY_MIN = 1;
    const COMPLEXITY_MAX = 5;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $dueAt;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $reporter;

    /**
     * @var int
     */
    private $complexity;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $status;

    /**
     * @param string    $title
     * @param string    $description
     * @param \DateTime $dueAt
     * @param string    $reporter
     * @param int       $complexity
     * @param string    $category
     * @param string    $status
     */
    public function __construct(
        $title,
        $description,
        $dueAt,
        $reporter,
        $complexity,
        $category,
        $status)
    {
        $this->createdAt = new \DateTime();
        $this->dueAt = $dueAt;
        $this->title = $title;
        $this->description = $description;
        $this->reporter = $reporter;
        $this->complexity = $complexity;
        $this->category = $category;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @return int
     */
    public function getComplexity()
    {
        return $this->complexity;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function startProgress()
    {
        if ($this->status !== self::STATUS_TODO) {
            throw new \RuntimeException("Only 'todo' items can start progress");
        }

        $this->status = self::STATUS_IN_PROGRESS;
    }

    public function complete()
    {
        if (false === in_array($this->status, [self::STATUS_IN_PROGRESS, self::STATUS_TODO])) {
            throw new \RuntimeException("Only 'in-progress' items can be completed");
        }

        $this->status = self::STATUS_DONE;
    }

    /**
     * @param string    $title
     * @param string    $description
     * @param \DateTime $dueAt
     * @param int       $complexity
     * @param string    $category
     */
    public function update(
        $title,
        $description,
        $dueAt,
        $complexity,
        $category)
    {
        $this->dueAt = $dueAt;
        $this->title = $title;
        $this->description = $description;
        $this->complexity = $complexity;
        $this->category = $category;
    }

    /**
     * @return string[]
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_TODO,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
        ];
    }
}
