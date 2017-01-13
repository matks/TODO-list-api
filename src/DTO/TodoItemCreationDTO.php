<?php

namespace TODOListApi\DTO;

class TodoItemCreationDTO extends TodoItemUpdateDTO
{
    /**
     * @var string
     */
    public $reporter;

    /**
     * @var string[]
     */
    public $availableReporters;

    /**
     * @param array $errors
     *
     * @return bool|array
     */
    public function isValid($errors = [])
    {
        if (false === in_array($this->reporter, $this->availableReporters)) {
            $errors[] = "Unknown reporter $this->reporter";
        }

        return parent::isValid($errors);
    }
}
