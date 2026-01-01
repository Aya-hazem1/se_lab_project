<?php

namespace App\Models;

class Task {
    public string $id;
    public string $title;
    public string $description;
    public string $deadline;
    public string $priority; // 'High', 'Medium', 'Low'
    public string $status;   // 'ToDo', 'Completed'

    public function __construct(string $title, string $description, string $deadline, string $priority) {
        $this->id = uniqid('task_'); // Generate a unique ID for the task
        $this->title = $title;
        $this->description = $description;
        $this->deadline = $deadline;
        $this->priority = $priority;
        $this->status = 'ToDo'; // FR5: Default status is 'ToDo'
    }
}
