<?php

namespace App\Services;

class FileStorageService {
    private static ?self $instance = null;
    private string $filePath;
    private array $tasks = [];

    // Private constructor to prevent direct instantiation
    private function __construct(string $filePath) {
        $this->filePath = $filePath;
        $this->loadTasks(); // FR10: Load tasks on start
    }

    // Singleton Pattern: Ensures only one instance of the storage manager exists.
    // Reason: To prevent data conflicts and ensure all parts of the app access the same task list.
    public static function getInstance(string $filePath): self {
        if (self::$instance === null) {
            self::$instance = new self($filePath);
        }
        return self::$instance;
    }

    private function loadTasks(): void {
        if (file_exists($this->filePath)) {
            $json = file_get_contents($this->filePath);
            // json_decode returns an array of objects, we need an associative array
            $decodedTasks = json_decode($json, true) ?? [];
            foreach ($decodedTasks as $task) {
                if (isset($task['id'])) {
                    $this->tasks[$task['id']] = $task;
                }
            }
        }
    }

    public function getTasks(): array {
        return $this->tasks;
    }

    public function addTask(array $task): void {
        $this->tasks[$task['id']] = $task;
    }

    public function saveTasks(): void {
        // Ensure the directory exists before writing
        if (!is_dir(dirname($this->filePath))) {
            mkdir(dirname($this->filePath), 0777, true);
        }
        file_put_contents($this->filePath, json_encode(array_values($this->tasks), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // FR9: Automatically save tasks when the script finishes execution.
    public function __destruct() {
        $this->saveTasks();
    }
}
