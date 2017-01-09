<?php

namespace TODOListApi\DTO;

use TODOListApi\Domain\TodoItem;

class TodoItemCreationDTO
{
    /**
     * @var \DateTime
     */
    public $due_at;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $reporter;

    /**
     * @var int
     */
    public $complexity;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string[]
     */
    public $availableReporters;

    /**
     * @return array|bool
     */
    public function isValid()
    {
        $errors = [];

        if (false === in_array($this->reporter, $this->availableReporters)) {
            $errors[] = "Unknown reporter $this->reporter";
        }

        $notBlank = [
            'title' => $this->title,
            'description' => $this->description,
            'reporter' => $this->reporter,
        ];
        foreach ($notBlank as $itemName => $itemValue) {
            if (!$itemValue) {
                $errors[] = "$itemName should not be blank";
            }
        }

        if (null !== $this->complexity) {
            if ($this->complexity < TodoItem::COMPLEXITY_MIN) {
                $errors[] = "$this->complexity is too low";
            }
            if ($this->complexity > TodoItem::COMPLEXITY_MAX) {
                $errors[] = "$this->complexity is too high";
            }
        }

        $testDate = new \DateTime($this->due_at);
        if (!$testDate) {
            $errors[] = "Date $this->due_at cannot be parsed";
        }

        if (empty($errors)) {
            return true;
        } else {
            return $errors;
        }
    }
}
