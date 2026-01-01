<?php

namespace App\Controllers;

use App\Models\Task;
use App\Services\FileStorageService;

class TaskController {
    private FileStorageService $storage;

    public function __construct() {
        // Initialize the storage service as 
        $this->storage = FileStorageService::getInstance(__DIR__ . '/../../data/tasks.json');
    }

    // FR1: Create a new task
    public function createTask(string $title, string $description, string $deadline, string $priority): void {
        $task = new Task($title, $description, $deadline, $priority);
        // Convert object to array for JSON storage
        $this->storage->addTask((array)$task);
    }

    // FR4, FR7, FR8: Get tasks with optional filtering
    public function getFilteredTasks(?string $statusFilter, ?string $priorityFilter): array {
        $tasks = $this->storage->getTasks();

        if ($statusFilter) {
            $tasks = array_filter($tasks, fn($task) => $task['status'] === $statusFilter);
        }

        if ($priorityFilter) {
            $tasks = array_filter($tasks, fn($task) => $task['priority'] === $priorityFilter);
        }

        return array_values($tasks);
    }
}
