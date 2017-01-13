<?php

namespace TODOListApi\DTO;

use TODOListApi\Domain\TodoItem;

class TodoItemUpdateDTO
{
    /**
     * @var \DateTime
     */
    public $dueAt;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $complexity;

    /**
     * @var string
     */
    public $category;

    /**
     * @param array $errors
     *
     * @return array|bool
     */
    public function isValid($errors = [])
    {
        $notBlank = [
            'title' => $this->title,
            'description' => $this->description,
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

        $testDate = new \DateTime($this->dueAt);
        if (!$testDate) {
            $errors[] = "Date $this->dueAt cannot be parsed";
        }

        if (empty($errors)) {
            return true;
        } else {
            return $errors;
        }
    }
}
