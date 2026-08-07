# GitHub Copilot Task Delegation System

This repository includes a system for delegating tasks to GitHub Copilot coding agent and monitoring their progress.

## Features

- **Task Delegation**: Trigger GitHub Actions workflow to create tracked tasks for Copilot
- **Progress Monitoring**: Automatically monitor task progress via GitHub Issues and Pull Requests
- **Web Dashboard**: View task status directly on the website
- **Automatic Status Updates**: Scheduled workflow checks task progress every hour

## How to Use

### Delegating a Task

1. **Via GitHub Actions UI**:
   - Go to the repository's "Actions" tab
   - Select "Delegate Task to Copilot" workflow
   - Click "Run workflow"
   - Enter your task description and priority
   - Click "Run workflow" to start

2. **Via Website**:
   - Visit the website and navigate to the "Copilot Tasks" section
   - Enter your task description
   - Select priority (low, medium, high)
   - Click "Delegate to Copilot"
   - Follow the instructions to complete delegation via GitHub Actions

### Monitoring Progress

1. **Via Website**:
   - The "Copilot Tasks" section shows all delegated tasks
   - Tasks are automatically refreshed every 30 seconds
   - Status indicators show:
     - 🔄 In Progress: Task is being worked on
     - ✅ Completed: Task is finished and merged

2. **Via GitHub Issues**:
   - All delegated tasks are tracked as GitHub Issues
   - Issues are labeled with `copilot-task` and priority
   - Progress updates are added as comments

3. **Via GitHub Actions**:
   - The "Monitor Copilot Progress" workflow runs hourly
   - Manual monitoring can be triggered via workflow dispatch
   - Status reports are saved in `.copilot-tasks/status-report.json`

## Workflows

### Delegate Task to Copilot
- **File**: `.github/workflows/copilot-delegate.yml`
- **Trigger**: Manual (workflow_dispatch)
- **Purpose**: Creates a tracked issue for Copilot to work on
- **Inputs**:
  - `task_description`: Description of the task
  - `task_priority`: Priority level (low/medium/high)

### Monitor Copilot Progress
- **File**: `.github/workflows/copilot-monitor.yml`
- **Trigger**: Scheduled (hourly) or Manual
- **Purpose**: Checks task progress and updates issue status
- **Actions**:
  - Finds all open copilot-task issues
  - Checks for related Pull Requests
  - Updates issue with PR status
  - Closes issues when PRs are merged
  - Generates status report

## Task Lifecycle

1. **Creation**: Task is delegated via workflow, creating a GitHub Issue
2. **Tracking**: Issue is labeled with `copilot-task` and priority
3. **Progress**: Copilot works on the task (may create PR)
4. **Monitoring**: Automatic checks link PRs to issues and update status
5. **Completion**: When PR is merged, issue is automatically closed

## Status Report

The monitoring workflow generates a status report at `.copilot-tasks/status-report.json` containing:
- Timestamp of last check
- Total number of active tasks
- Details of each task (number, title, creation date, labels)

## Labels

- `copilot-task`: Identifies all Copilot-delegated tasks
- `priority-low`: Low priority tasks
- `priority-medium`: Medium priority tasks
- `priority-high`: High priority tasks
- `completed`: Tasks that have been successfully completed

## Requirements

- GitHub Actions enabled on the repository
- Appropriate permissions for workflow to create issues and access PRs
- Repository must be accessible via GitHub API for web dashboard

## Future Enhancements

- Direct workflow triggering from web interface (requires GitHub API token)
- More detailed progress tracking with subtasks
- Notifications for task completion
- Integration with GitHub Projects for better task management
- Statistics and analytics dashboard
