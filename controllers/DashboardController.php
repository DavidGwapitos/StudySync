<?php
/**
 * Dashboard Controller
 * Handles dashboard-related operations
 * Manages statistics and summary data display
 * 
 * @package StudySync
 * @subpackage Controllers
 */

class DashboardController {
    private $taskController;
    private $user_id;

    /**
     * Constructor
     * 
     * @param TaskController $taskController Task controller instance
     * @param int $user_id Current user's ID
     */
    public function __construct($taskController, $user_id) {
        $this->taskController = $taskController;
        $this->user_id = $user_id;
    }

    /**
     * Get dashboard data
     * Retrieves all data needed for dashboard display
     * 
     * @return array Dashboard data including statistics and recent tasks
     */
    public function getDashboardData() {
        // Get task statistics
        $stats = $this->taskController->getStatistics();

        // Get upcoming tasks (next 7 days)
        $upcoming_tasks = $this->taskController->getUpcomingTasks(7);

        // Get recent tasks (limit to 5)
        $recent_tasks = $this->taskController->getTasks([], 1);
        $recent_tasks = array_slice($recent_tasks['tasks'], 0, 5);

        // Get available subjects
        $subjects = $this->taskController->getSubjects();

        return [
            'stats' => $stats,
            'upcoming_tasks' => $upcoming_tasks,
            'recent_tasks' => $recent_tasks,
            'subjects' => $subjects
        ];
    }

    /**
     * Get statistics summary
     * Retrieves only the statistics data
     * 
     * @return array Statistics data
     */
    public function getStatistics() {
        return $this->taskController->getStatistics();
    }

    /**
     * Get completion percentage
     * Calculates the percentage of completed tasks
     * 
     * @return float Completion percentage (0-100)
     */
    public function getCompletionPercentage() {
        $stats = $this->getStatistics();
        
        if ($stats['total_tasks'] === 0) {
            return 0;
        }

        return ($stats['completed_tasks'] / $stats['total_tasks']) * 100;
    }

    /**
     * Get upcoming tasks
     * 
     * @return array Array of upcoming tasks
     */
    public function getUpcomingTasks() {
        return $this->taskController->getUpcomingTasks(7);
    }

    /**
     * Get task breakdown by priority
     * 
     * @return array Task counts grouped by priority
     */
    public function getTasksByPriority() {
        $breakdown = [
            'Low' => 0,
            'Medium' => 0,
            'High' => 0
        ];

        // Get all tasks
        $all_tasks = $this->taskController->getTasks([], 1);
        
        foreach ($all_tasks['tasks'] as $task) {
            if (isset($breakdown[$task['priority']])) {
                $breakdown[$task['priority']]++;
            }
        }

        return $breakdown;
    }

    /**
     * Get task breakdown by subject
     * 
     * @return array Task counts grouped by subject
     */
    public function getTasksBySubject() {
        $breakdown = [];
        $subjects = $this->taskController->getSubjects();

        foreach ($subjects as $subject) {
            $all_tasks = $this->taskController->getTasks(['subject' => $subject], 1);
            $breakdown[$subject] = $all_tasks['total'];
        }

        return $breakdown;
    }
}
?>
