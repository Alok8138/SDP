// Fake Server Simulation - Async Operations
const FakeServer = {
    // Simulate network delay
    delay(ms = 500) {
        return new Promise(resolve => setTimeout(resolve, ms));
    },

    // Simulate async email validation
    async checkEmailUnique(email) {
        await this.delay(300);

        const exists = globalState.interns.some(intern =>
            intern.email.toLowerCase() === email.toLowerCase()
        );

        return !exists;
    },

    // Simulate async skill validation
    async validateSkills(skills) {
        await this.delay(200);

        const validSkills = ['JavaScript', 'React', 'HTML', 'CSS', 'Node.js', 'Git'];
        const invalidSkills = skills.filter(skill => !validSkills.includes(skill));

        return {
            isValid: invalidSkills.length === 0,
            invalidSkills,
            validSkills: skills.filter(skill => validSkills.includes(skill))
        };
    },

    // Simulate async intern creation
    async createIntern(internData) {
        await this.delay(800);

        // Validate required fields
        if (!internData.name || !internData.email || !internData.skills) {
            throw new Error('Missing required fields');
        }

        // Check email uniqueness
        const isEmailUnique = await this.checkEmailUnique(internData.email);
        if (!isEmailUnique) {
            throw new Error('Email already exists');
        }

        // Validate skills
        const skillValidation = await this.validateSkills(internData.skills);
        if (!skillValidation.isValid) {
            throw new Error(`Invalid skills: ${skillValidation.invalidSkills.join(', ')}`);
        }

        // Create intern
        const intern = StateManager.addIntern({
            ...internData,
            skills: skillValidation.validSkills
        });

        return {
            success: true,
            message: 'Intern created successfully',
            data: intern
        };
    },

    // Simulate async task creation
    async createTask(taskData) {
        await this.delay(800);

        // Validate required fields
        if (!taskData.title || !taskData.description || !taskData.estimatedHours || !taskData.requiredSkills) {
            throw new Error('Missing required fields');
        }

        // Validate skills
        const skillValidation = await this.validateSkills(taskData.requiredSkills);
        if (!skillValidation.isValid) {
            throw new Error(`Invalid skills: ${skillValidation.invalidSkills.join(', ')}`);
        }

        // Validate dependencies
        if (taskData.dependencies && taskData.dependencies.length > 0) {
            const invalidDeps = taskData.dependencies.filter(depId =>
                !globalState.tasks.some(task => task.id === depId)
            );

            if (invalidDeps.length > 0) {
                throw new Error(`Invalid dependencies: ${invalidDeps.join(', ')}`);
            }
        }

        // Create task
        const task = StateManager.addTask({
            ...taskData,
            requiredSkills: skillValidation.validSkills,
            estimatedHours: parseInt(taskData.estimatedHours)
        });

        return {
            success: true,
            message: 'Task created successfully',
            data: task
        };
    },

    // Simulate async task update
    async updateTask(taskId, taskData) {
        await this.delay(800);

        const task = StateManager.getTaskById(taskId);
        if (!task) {
            throw new Error('Task not found');
        }

        // Validate required fields
        if (!taskData.title || !taskData.description || !taskData.estimatedHours || !taskData.requiredSkills) {
            throw new Error('Missing required fields');
        }

        // Validate skills
        const skillValidation = await this.validateSkills(taskData.requiredSkills);
        if (!skillValidation.isValid) {
            throw new Error(`Invalid skills: ${skillValidation.invalidSkills.join(', ')}`);
        }

        // Validate dependencies
        if (taskData.dependencies && taskData.dependencies.length > 0) {
            // Filter out self-reference
            if (taskData.dependencies.includes(taskId)) {
                throw new Error('Task cannot depend on itself');
            }

            const invalidDeps = taskData.dependencies.filter(depId =>
                !globalState.tasks.some(t => t.id === depId)
            );

            if (invalidDeps.length > 0) {
                throw new Error(`Invalid dependencies: ${invalidDeps.join(', ')}`);
            }

            // Check circular
            const validation = await this.validateTaskDependencies(taskId, taskData.dependencies);
            if (!validation.success) {
                throw new Error('Circular dependency detected');
            }
        }

        // Update task
        const success = StateManager.updateTaskDetails(taskId, {
            ...taskData,
            requiredSkills: skillValidation.validSkills,
            estimatedHours: parseInt(taskData.estimatedHours)
        });

        if (!success) {
            throw new Error('Failed to update task');
        }

        return {
            success: true,
            message: 'Task updated successfully'
        };
    },

    // Simulate async status update
    async updateInternStatus(internId, newStatus) {
        await this.delay(500);

        const success = StateManager.updateInternStatus(internId, newStatus);

        if (!success) {
            throw new Error('Intern not found');
        }

        return {
            success: true,
            message: `Intern status updated to ${newStatus}`
        };
    },

    // Simulate async task assignment
    async assignTask(taskId, internId) {
        await this.delay(600);

        // Check if task exists
        const task = StateManager.getTaskById(taskId);
        if (!task) {
            throw new Error(`Task ${taskId} not found`);
        }

        // Check if intern exists and is active
        const intern = StateManager.getInternById(internId);
        if (!intern) {
            throw new Error(`Intern ${internId} not found`);
        }

        if (intern.status !== 'ACTIVE') {
            throw new Error('Only ACTIVE interns can be assigned tasks');
        }

        // Check if intern has required skills
        const missingSkills = task.requiredSkills.filter(skill =>
            !intern.skills.includes(skill)
        );

        if (missingSkills.length > 0) {
            throw new Error(`Intern missing required skills: ${missingSkills.join(', ')}`);
        }

        // Check if task is already assigned
        if (task.assignedTo) {
            throw new Error(`Task is already assigned to intern ${task.assignedTo}`);
        }

        // Assign task
        const success = StateManager.assignTask(taskId, internId);

        if (!success) {
            throw new Error('Failed to assign task');
        }

        return {
            success: true,
            message: `Task ${taskId} assigned to ${intern.name}`
        };
    },



    // Simulate async task status update
    async updateTaskStatus(taskId, newStatus) {
        await this.delay(400);

        const task = StateManager.getTaskById(taskId);
        if (!task) {
            throw new Error('Task not found');
        }

        // Validate status transition
        const validTransitions = {
            'PENDING': ['ASSIGNED', 'IN_PROGRESS'],
            'ASSIGNED': ['IN_PROGRESS', 'PENDING'],
            'IN_PROGRESS': ['DONE', 'ASSIGNED'],
            'DONE': []
        };

        if (!validTransitions[task.status]?.includes(newStatus)) {
            throw new Error(`Invalid status transition from ${task.status} to ${newStatus}`);
        }

        // Check dependencies if moving to DONE
        if (newStatus === 'DONE' && task.dependencies.length > 0) {
            const unresolvedDeps = task.dependencies.filter(depId => {
                const depTask = StateManager.getTaskById(depId);
                return !depTask || depTask.status !== 'DONE';
            });

            if (unresolvedDeps.length > 0) {
                throw new Error(`Cannot complete task. Unresolved dependencies: ${unresolvedDeps.join(', ')}`);
            }
        }

        const success = StateManager.updateTaskStatus(taskId, newStatus);

        if (!success) {
            throw new Error('Failed to update task status');
        }

        return {
            success: true,
            message: `Task status updated to ${newStatus}`
        };
    },

    // Simulate async data fetch
    async fetchData(type, filters = {}) {
        await this.delay(300);

        switch (type) {
            case 'interns':
                let interns = globalState.interns;

                if (filters.status && filters.status !== 'all') {
                    interns = interns.filter(i => i.status === filters.status);
                }

                if (filters.skills && filters.skills.length > 0) {
                    interns = interns.filter(i =>
                        filters.skills.every(skill => i.skills.includes(skill))
                    );
                }

                return {
                    success: true,
                    data: interns
                };

            case 'tasks':
                return {
                    success: true,
                    data: globalState.tasks
                };

            case 'stats':
                return {
                    success: true,
                    data: StateManager.getStats()
                };

            case 'logs':
                return {
                    success: true,
                    data: globalState.logs.slice(0, filters.limit || 50)
                };

            default:
                throw new Error('Invalid data type');
        }
    },

    // Simulate async search
    async searchInterns(query) {
        await this.delay(400);

        const results = globalState.interns.filter(intern =>
            intern.name.toLowerCase().includes(query.toLowerCase()) ||
            intern.email.toLowerCase().includes(query.toLowerCase()) ||
            intern.id.toLowerCase().includes(query.toLowerCase())
        );

        return {
            success: true,
            data: results
        };
    },

    // Simulate async validation
    async validateTaskDependencies(taskId, dependencies) {
        await this.delay(300);

        // Check for circular dependencies
        const visited = new Set();
        const hasCircular = this._checkCircularDependency(taskId, dependencies, visited);

        if (hasCircular) {
            throw new Error('Circular dependency detected');
        }

        // Check if dependencies exist
        const invalidDeps = dependencies.filter(depId =>
            !globalState.tasks.some(task => task.id === depId)
        );

        if (invalidDeps.length > 0) {
            throw new Error(`Invalid dependencies: ${invalidDeps.join(', ')}`);
        }

        return {
            success: true,
            message: 'Dependencies are valid'
        };
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
    }
};