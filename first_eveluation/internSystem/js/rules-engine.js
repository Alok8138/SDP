// Business Rules Engine
const RulesEngine = {
    // Intern status transition rules
    canChangeInternStatus(internId, newStatus) {
        const intern = StateManager.getInternById(internId);
        if (!intern) return { allowed: false, reason: 'Intern not found' };
        
        const currentStatus = intern.status;
        
        // Define allowed transitions
        const allowedTransitions = {
            'ONBOARDING': ['ACTIVE', 'EXITED'],
            'ACTIVE': ['EXITED'],
            'EXITED': [] // Cannot return from EXITED
        };
        
        const isAllowed = allowedTransitions[currentStatus]?.includes(newStatus) || false;
        
        return {
            allowed: isAllowed,
            reason: isAllowed ? '' : `Cannot transition from ${currentStatus} to ${newStatus}`
        };
    },
    
    // Task assignment rules
    canAssignTask(taskId, internId) {
        const task = StateManager.getTaskById(taskId);
        const intern = StateManager.getInternById(internId);
        
        if (!task) return { allowed: false, reason: 'Task not found' };
        if (!intern) return { allowed: false, reason: 'Intern not found' };
        
        // Rule 1: Intern must be ACTIVE
        if (intern.status !== 'ACTIVE') {
            return { allowed: false, reason: 'Only ACTIVE interns can be assigned tasks' };
        }
        
        // Rule 2: Task must not be already assigned
        if (task.assignedTo) {
            return { allowed: false, reason: 'Task is already assigned' };
        }
        
        // Rule 3: Intern must have all required skills
        const missingSkills = task.requiredSkills.filter(skill => 
            !intern.skills.includes(skill)
        );
        
        if (missingSkills.length > 0) {
            return { 
                allowed: false, 
                reason: `Missing required skills: ${missingSkills.join(', ')}` 
            };
        }
        
        // Rule 4: Intern cannot have more than 3 active tasks
        const activeTasks = StateManager.getTasksForIntern(internId).filter(t => 
            t.status !== 'DONE'
        ).length;
        
        if (activeTasks >= 3) {
            return { allowed: false, reason: 'Intern has reached maximum active tasks (3)' };
        }
        
        return { allowed: true, reason: '' };
    },
    
    // Task status transition rules
    canChangeTaskStatus(taskId, newStatus) {
        const task = StateManager.getTaskById(taskId);
        if (!task) return { allowed: false, reason: 'Task not found' };
        
        const currentStatus = task.status;
        
        // Define allowed transitions
        const allowedTransitions = {
            'PENDING': ['ASSIGNED', 'IN_PROGRESS'],
            'ASSIGNED': ['IN_PROGRESS', 'PENDING'],
            'IN_PROGRESS': ['DONE', 'ASSIGNED'],
            'DONE': []
        };
        
        if (!allowedTransitions[currentStatus]?.includes(newStatus)) {
            return { 
                allowed: false, 
                reason: `Cannot transition from ${currentStatus} to ${newStatus}` 
            };
        }
        
        // Special rule for DONE status
        if (newStatus === 'DONE') {
            // Check if all dependencies are done
            const unresolvedDeps = task.dependencies.filter(depId => {
                const depTask = StateManager.getTaskById(depId);
                return !depTask || depTask.status !== 'DONE';
            });
            
            if (unresolvedDeps.length > 0) {
                return { 
                    allowed: false, 
                    reason: `Cannot complete task. Unresolved dependencies: ${unresolvedDeps.join(', ')}` 
                };
            }
        }
        
        return { allowed: true, reason: '' };
    },
    
    // Dependency validation rules
    validateDependencies(taskId, dependencies) {
        // Check for circular dependencies
        const visited = new Set();
        const hasCircular = this._checkCircularDependency(taskId, dependencies, visited);
        
        if (hasCircular) {
            return { valid: false, reason: 'Circular dependency detected' };
        }
        
        // Check if dependencies exist
        const invalidDeps = dependencies.filter(depId => 
            !StateManager.getTaskById(depId)
        );
        
        if (invalidDeps.length > 0) {
            return { 
                valid: false, 
                reason: `Invalid dependencies: ${invalidDeps.join(', ')}` 
            };
        }
        
        // Check if task depends on itself
        if (dependencies.includes(taskId)) {
            return { valid: false, reason: 'Task cannot depend on itself' };
        }
        
        return { valid: true, reason: '' };
    },
    
    // Helper for circular dependency check
    _checkCircularDependency(currentTaskId, dependencies, visited) {
        if (dependencies.includes(currentTaskId)) {
            return true;
        }
        
        for (const depId of dependencies) {
            if (visited.has(depId)) continue;
            
            visited.add(depId);
            const depTask = StateManager.getTaskById(depId);
            
            if (depTask && depTask.dependencies) {
                if (this._checkCircularDependency(currentTaskId, depTask.dependencies, visited)) {
                    return true;
                }
            }
        }
        
        return false;
    },
    
    // Intern creation validation
    validateInternCreation(internData) {
        const errors = [];
        
        // Name validation
        if (!internData.name || internData.name.trim().length < 2) {
            errors.push('Name must be at least 2 characters long');
        }
        
        // Email validation
        if (!internData.email) {
            errors.push('Email is required');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(internData.email)) {
            errors.push('Invalid email format');
        }
        
        // Skills validation
        if (!internData.skills || internData.skills.length === 0) {
            errors.push('At least one skill is required');
        } else {
            const validSkills = ['JavaScript', 'React', 'HTML', 'CSS', 'Node.js', 'Git'];
            const invalidSkills = internData.skills.filter(skill => !validSkills.includes(skill));
            
            if (invalidSkills.length > 0) {
                errors.push(`Invalid skills: ${invalidSkills.join(', ')}`);
            }
        }
        
        // Status validation
        const validStatuses = ['ONBOARDING', 'ACTIVE'];
        if (internData.status && !validStatuses.includes(internData.status)) {
            errors.push(`Invalid status. Must be one of: ${validStatuses.join(', ')}`);
        }
        
        return {
            isValid: errors.length === 0,
            errors
        };
    },
    
    // Task creation validation
    validateTaskCreation(taskData) {
        const errors = [];
        
        // Title validation
        if (!taskData.title || taskData.title.trim().length < 3) {
            errors.push('Title must be at least 3 characters long');
        }
        
        // Description validation
        if (!taskData.description || taskData.description.trim().length < 10) {
            errors.push('Description must be at least 10 characters long');
        }
        
        // Hours validation
        if (!taskData.estimatedHours) {
            errors.push('Estimated hours are required');
        } else if (isNaN(taskData.estimatedHours) || taskData.estimatedHours < 1 || taskData.estimatedHours > 100) {
            errors.push('Estimated hours must be between 1 and 100');
        }
        
        // Skills validation
        if (!taskData.requiredSkills || taskData.requiredSkills.length === 0) {
            errors.push('At least one required skill is required');
        } else {
            const validSkills = ['JavaScript', 'React', 'HTML', 'CSS', 'Node.js', 'Git'];
            const invalidSkills = taskData.requiredSkills.filter(skill => !validSkills.includes(skill));
            
            if (invalidSkills.length > 0) {
                errors.push(`Invalid skills: ${invalidSkills.join(', ')}`);
            }
        }
        
        return {
            isValid: errors.length === 0,
            errors
        };
    },
    
    // Get eligible interns for a task
    getEligibleInterns(taskId) {
        const task = StateManager.getTaskById(taskId);
        if (!task) return [];
        
        return globalState.interns.filter(intern => {
            // Must be ACTIVE
            if (intern.status !== 'ACTIVE') return false;
            
            // Must have all required skills
            const hasAllSkills = task.requiredSkills.every(skill => 
                intern.skills.includes(skill)
            );
            
            if (!hasAllSkills) return false;
            
            // Cannot have task already assigned
            if (task.assignedTo === intern.id) return false;
            
            // Check active task limit
            const activeTasks = StateManager.getTasksForIntern(intern.id).filter(t => 
                t.status !== 'DONE'
            ).length;
            
            return activeTasks < 3;
        });
    },
    
    // Calculate total estimated hours for an intern
    calculateTotalHours(internId) {
        const tasks = StateManager.getTasksForIntern(internId);
        return tasks.reduce((total, task) => total + task.estimatedHours, 0);
    },
    
    // Check if task can be deleted
    canDeleteTask(taskId) {
        const task = StateManager.getTaskById(taskId);
        if (!task) return { allowed: false, reason: 'Task not found' };
        
        // Cannot delete if assigned
        if (task.assignedTo) {
            return { allowed: false, reason: 'Cannot delete assigned task' };
        }
        
        // Check if other tasks depend on this task
        const dependentTasks = globalState.tasks.filter(t => 
            t.dependencies.includes(taskId)
        );
        
        if (dependentTasks.length > 0) {
            const dependentTaskIds = dependentTasks.map(t => t.id);
            return { 
                allowed: false, 
                reason: `Other tasks depend on this task: ${dependentTaskIds.join(', ')}` 
            };
        }
        
        return { allowed: true, reason: '' };
    }
};