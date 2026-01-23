// Global State - Single Source of Truth
const globalState = {
    interns: [],
    tasks: [],
    logs: [],
    currentView: 'dashboard',
    isLoading: false,
    errors: [],
    nextTaskId: 1,
    currentRole: 'HR', // 'HR' or 'INTERN'
    currentInternId: null, // For intern login

    // Sample initial data
    sampleInterns: [
        {
            id: '2026-0001',
            name: 'John Smith',
            email: 'john.smith@example.com',
            skills: ['JavaScript', 'React', 'HTML'],
            status: 'ACTIVE',
            assignedTasks: [],
            createdAt: new Date('2026-01-15').toISOString()
        },
        {
            id: '2026-0002',
            name: 'Emma Johnson',
            email: 'emma.j@example.com',
            skills: ['CSS', 'HTML', 'Git'],
            status: 'ONBOARDING',
            assignedTasks: [],
            createdAt: new Date('2026-01-18').toISOString()
        },
        {
            id: '2026-0003',
            name: 'Michael Chen',
            email: 'm.chen@example.com',
            skills: ['JavaScript', 'Node.js', 'Git'],
            status: 'ACTIVE',
            assignedTasks: [],
            createdAt: new Date('2026-01-10').toISOString()
        },
        {
            id: '2026-0004',
            name: 'Sarah Williams',
            email: 'sarah.w@example.com',
            skills: ['React', 'CSS', 'JavaScript'],
            status: 'EXITED',
            assignedTasks: [],
            createdAt: new Date('2025-12-20').toISOString()
        }
    ],

    sampleTasks: [
        {
            id: 'T001',
            title: 'Fix Navigation Bug',
            description: 'Fix the navigation menu collapse issue on mobile devices',
            estimatedHours: 4,
            requiredSkills: ['JavaScript', 'CSS'],
            status: 'ASSIGNED',
            assignedTo: '2026-0001',
            dependencies: [],
            createdAt: new Date('2026-01-18').toISOString(),
            updatedAt: new Date('2026-01-19').toISOString()
        },
        {
            id: 'T002',
            title: 'Implement User Dashboard',
            description: 'Create a new dashboard component with user statistics',
            estimatedHours: 12,
            requiredSkills: ['React', 'JavaScript', 'CSS'],
            status: 'PENDING',
            assignedTo: null,
            dependencies: [],
            createdAt: new Date('2026-01-19').toISOString(),
            updatedAt: new Date('2026-01-19').toISOString()
        },
        {
            id: 'T003',
            title: 'API Integration Setup',
            description: 'Set up API service layer and error handling',
            estimatedHours: 8,
            requiredSkills: ['JavaScript', 'Node.js'],
            status: 'IN_PROGRESS',
            assignedTo: '2026-0003',
            dependencies: [],
            createdAt: new Date('2026-01-17').toISOString(),
            updatedAt: new Date('2026-01-18').toISOString()
        },
        {
            id: 'T004',
            title: 'Database Schema Design',
            description: 'Design and document the database schema',
            estimatedHours: 6,
            requiredSkills: ['Node.js'],
            status: 'DONE',
            assignedTo: '2026-0003',
            dependencies: [],
            createdAt: new Date('2026-01-10').toISOString(),
            updatedAt: new Date('2026-01-15').toISOString()
        }
    ]
};

// State Management Functions
// State Management Functions
const StateManager = {
    // Initialize state
    initialize() {
        // Load from localStorage if available
        const savedState = localStorage.getItem('internOpsState');
        if (savedState) {
            const parsed = JSON.parse(savedState);
            globalState.interns = parsed.interns || [];
            globalState.tasks = parsed.tasks || [];
            globalState.logs = parsed.logs || [];
            globalState.nextTaskId = parsed.nextTaskId || 1;
            globalState.currentRole = parsed.currentRole || 'HR';
            globalState.currentInternId = parsed.currentInternId || null;
        } else {
            // Use sample data
            globalState.interns = [...globalState.sampleInterns];
            globalState.tasks = [...globalState.sampleTasks];
            globalState.nextTaskId = 1000;
            globalState.currentRole = 'HR';
            globalState.currentInternId = null;
        }

        // Add log for initialization
        this.addLog('SYSTEM', 'Application initialized', { timestamp: new Date().toISOString() });
    },

    // Save state to localStorage
    saveState() {
        const stateToSave = {
            interns: globalState.interns,
            tasks: globalState.tasks,
            logs: globalState.logs.slice(-100),
            nextTaskId: globalState.nextTaskId,
            currentRole: globalState.currentRole,
            currentInternId: globalState.currentInternId
        };
        localStorage.setItem('internOpsState', JSON.stringify(stateToSave));
    },

    // Set current role
    setRole(role, internId = null) {
        globalState.currentRole = role;
        globalState.currentInternId = internId;
        this.saveState();
        this.addLog('ROLE_CHANGE', `Changed role to ${role}`, { role, internId });
    },

    // Get current role
    getCurrentRole() {
        return globalState.currentRole;
    },

    // Get current intern (if logged in as intern)
    getCurrentIntern() {
        if (globalState.currentRole === 'INTERN' && globalState.currentInternId) {
            return this.getInternById(globalState.currentInternId);
        }
        return null;
    },

    // ... rest of your existing functions remain the same ...












    // Initialize state with sample data and role info
    initialize() {
        // Load from localStorage if available
        const savedState = localStorage.getItem('internOpsState');
        if (savedState) {
            const parsed = JSON.parse(savedState);
            globalState.interns = parsed.interns || [];
            globalState.tasks = parsed.tasks || [];
            globalState.logs = parsed.logs || [];
            globalState.nextTaskId = parsed.nextTaskId || 1;
            globalState.currentRole = parsed.currentRole || null;
            globalState.currentInternId = parsed.currentInternId || null;
        } else {
            // Use sample data but no one is logged in yet
            globalState.interns = [...globalState.sampleInterns];
            globalState.tasks = [...globalState.sampleTasks];
            globalState.nextTaskId = 1000; // Start from T1000 for new tasks
            globalState.currentRole = null;
            globalState.currentInternId = null;
        }

        // Add log for initialization
        this.addLog('SYSTEM', 'Application initialized', { timestamp: new Date().toISOString() });
    },

    // Save state to localStorage (including role info)
    saveState() {
        const stateToSave = {
            interns: globalState.interns,
            tasks: globalState.tasks,
            logs: globalState.logs.slice(-100), // Keep last 100 logs
            nextTaskId: globalState.nextTaskId,
            currentRole: globalState.currentRole,
            currentInternId: globalState.currentInternId
        };
        localStorage.setItem('internOpsState', JSON.stringify(stateToSave));
    },

    // Add a log entry
    addLog(action, details, metadata = {}) {
        const logEntry = {
            id: `LOG${Date.now()}`,
            timestamp: new Date().toISOString(),
            action,
            details,
            metadata
        };

        globalState.logs.unshift(logEntry);

        // Keep only last 500 logs
        if (globalState.logs.length > 500) {
            globalState.logs = globalState.logs.slice(0, 500);
        }

        this.saveState();
        return logEntry;
    },

    // Add an intern
    addIntern(internData) {
        const internId = this.generateInternId();
        const intern = {
            id: internId,
            name: internData.name,
            email: internData.email,
            skills: internData.skills,
            status: internData.status || 'ONBOARDING',
            assignedTasks: [],
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };

        globalState.interns.push(intern);
        this.addLog('INTERN_CREATED', `Created intern ${internId} - ${intern.name}`, { internId });
        this.saveState();
        return intern;
    },

    // Generate intern ID
    generateInternId() {
        const year = new Date().getFullYear();
        const sequence = globalState.interns.filter(i => i.id.startsWith(year)).length + 1;
        return `${year}-${sequence.toString().padStart(4, '0')}`;
    },

    // Add a task
    addTask(taskData) {
        const taskId = `T${globalState.nextTaskId.toString().padStart(4, '0')}`;
        const task = {
            id: taskId,
            title: taskData.title,
            description: taskData.description,
            estimatedHours: parseInt(taskData.estimatedHours),
            requiredSkills: taskData.requiredSkills,
            status: 'PENDING',
            assignedTo: null,
            dependencies: taskData.dependencies || [],
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };

        globalState.tasks.push(task);
        globalState.nextTaskId++;

        this.addLog('TASK_CREATED', `Created task ${taskId} - ${task.title}`, { taskId });
        this.saveState();
        return task;
    },

    // Update intern status
    // updateInternStatus(internId, newStatus) {
    //     const intern = globalState.interns.find(i => i.id === internId);
    //     if (!intern) return false;

    //     const oldStatus = intern.status;
    //     intern.status = newStatus;
    //     intern.updatedAt = new Date().toISOString();

    //     this.addLog('INTERN_STATUS_CHANGED', 
    //         `Changed ${intern.name} status from ${oldStatus} to ${newStatus}`,
    //         { internId, oldStatus, newStatus }
    //     );
    //     this.saveState();
    //     return true;
    // },







    // Update intern status - SIMPLE VERSION
    updateInternStatus(internId, newStatus) {
        const intern = globalState.interns.find(i => i.id === internId);
        if (!intern) {
            console.error('Intern not found:', internId);
            return false;
        }

        const oldStatus = intern.status;

        // Basic validation
        if (oldStatus === 'EXITED' && newStatus === 'ACTIVE') {
            console.error('Cannot reactivate EXITED intern');
            return false;
        }

        // Update status
        intern.status = newStatus;
        intern.updatedAt = new Date().toISOString();

        this.addLog('INTERN_STATUS_CHANGED',
            `Changed ${intern.name} status from ${oldStatus} to ${newStatus}`,
            { internId, oldStatus, newStatus }
        );

        this.saveState();
        return true;
    },












    // Update task status
    updateTaskStatus(taskId, newStatus) {
        const task = globalState.tasks.find(t => t.id === taskId);
        if (!task) return false;

        const oldStatus = task.status;
        task.status = newStatus;
        task.updatedAt = new Date().toISOString();

        this.addLog('TASK_STATUS_CHANGED',
            `Changed task ${taskId} status from ${oldStatus} to ${newStatus}`,
            { taskId, oldStatus, newStatus }
        );
        this.saveState();
        return true;
    },

    // Update task details
    updateTaskDetails(taskId, updates) {
        const task = globalState.tasks.find(t => t.id === taskId);
        if (!task) return false;

        // Apply updates
        if (updates.title) task.title = updates.title;
        if (updates.description) task.description = updates.description;
        if (updates.estimatedHours) task.estimatedHours = parseInt(updates.estimatedHours);
        if (updates.requiredSkills) task.requiredSkills = updates.requiredSkills;
        if (updates.dependencies) task.dependencies = updates.dependencies;

        task.updatedAt = new Date().toISOString();

        this.addLog('TASK_UPDATED',
            `Updated details for task ${taskId}`,
            { taskId, updates }
        );
        this.saveState();
        return true;
    },

    // Assign task to intern
    // assignTask(taskId, internId) {
    //     const task = globalState.tasks.find(t => t.id === taskId);
    //     const intern = globalState.interns.find(i => i.id === internId);

    //     if (!task || !intern) return false;

    //     // Update task
    //     task.assignedTo = internId;
    //     task.status = 'ASSIGNED';
    //     task.updatedAt = new Date().toISOString();

    //     // Update intern
    //     if (!intern.assignedTasks.includes(taskId)) {
    //         intern.assignedTasks.push(taskId);
    //     }

    //     this.addLog('TASK_ASSIGNED',
    //         `Assigned task ${taskId} to ${intern.name}`,
    //         { taskId, internId }
    //     );
    //     this.saveState();
    //     return true;
    // },










    // Assign task to intern
    assignTask(taskId, internId) {
        const task = globalState.tasks.find(t => t.id === taskId);
        const intern = globalState.interns.find(i => i.id === internId);

        if (!task) {
            console.error('Task not found:', taskId);
            return false;
        }
        if (!intern) {
            console.error('Intern not found:', internId);
            return false;
        }

        // Update task
        task.assignedTo = internId;
        task.status = 'ASSIGNED';
        task.updatedAt = new Date().toISOString();

        // Update intern - ensure assignedTasks is an array
        if (!Array.isArray(intern.assignedTasks)) {
            intern.assignedTasks = [];
        }

        if (!intern.assignedTasks.includes(taskId)) {
            intern.assignedTasks.push(taskId);
        }

        this.addLog('TASK_ASSIGNED',
            `Assigned task ${taskId} to ${intern.name}`,
            { taskId, internId }
        );
        this.saveState();
        return true;
    },


















    // Unassign task
    unassignTask(taskId) {
        const task = globalState.tasks.find(t => t.id === taskId);
        if (!task || !task.assignedTo) return false;

        const intern = globalState.interns.find(i => i.id === task.assignedTo);
        if (intern) {
            intern.assignedTasks = intern.assignedTasks.filter(t => t !== taskId);
        }

        const oldAssignee = task.assignedTo;
        task.assignedTo = null;
        task.status = 'PENDING';
        task.updatedAt = new Date().toISOString();

        this.addLog('TASK_UNASSIGNED',
            `Unassigned task ${taskId} from ${oldAssignee}`,
            { taskId, oldAssignee }
        );
        this.saveState();
        return true;
    },

    // Update task dependencies
    updateTaskDependencies(taskId, dependencies) {
        const task = globalState.tasks.find(t => t.id === taskId);
        if (!task) return false;

        task.dependencies = dependencies;
        task.updatedAt = new Date().toISOString();

        this.addLog('TASK_DEPENDENCIES_UPDATED',
            `Updated dependencies for task ${taskId}`,
            { taskId, dependencies }
        );
        this.saveState();
        return true;
    },

    // Get intern by ID
    getInternById(internId) {
        return globalState.interns.find(i => i.id === internId);
    },

    // Get task by ID
    getTaskById(taskId) {
        return globalState.tasks.find(t => t.id === taskId);
    },

    // Get interns by status
    getInternsByStatus(status) {
        if (status === 'all') return globalState.interns;
        return globalState.interns.filter(intern => intern.status === status);
    },

    // Get interns by skills
    getInternsBySkills(skills) {
        if (!skills || skills.length === 0) return globalState.interns;
        return globalState.interns.filter(intern =>
            skills.every(skill => intern.skills.includes(skill))
        );
    },

    // Get tasks by status
    getTasksByStatus(status) {
        if (status === 'all') return globalState.tasks;
        return globalState.tasks.filter(task => task.status === status);
    },

    // Get eligible interns for task
    getEligibleInternsForTask(taskId) {
        const task = this.getTaskById(taskId);
        if (!task) return [];

        return globalState.interns.filter(intern =>
            intern.status === 'ACTIVE' &&
            task.requiredSkills.every(skill => intern.skills.includes(skill))
        );
    },

    // Get tasks for intern
    getTasksForIntern(internId) {
        return globalState.tasks.filter(task => task.assignedTo === internId);
    },

    // Get all active assignments
    getActiveAssignments() {
        return globalState.tasks.filter(task => task.assignedTo && task.status !== 'DONE');
    },

    // Get system statistics
    getStats() {
        const totalInterns = globalState.interns.length;
        const activeInterns = globalState.interns.filter(i => i.status === 'ACTIVE').length;
        const totalTasks = globalState.tasks.length;
        const completedTasks = globalState.tasks.filter(t => t.status === 'DONE').length;
        const pendingTasks = globalState.tasks.filter(t => t.status === 'PENDING').length;

        return {
            totalInterns,
            activeInterns,
            totalTasks,
            completedTasks,
            pendingTasks
        };
    },

    // Set loading state
    setLoading(isLoading) {
        globalState.isLoading = isLoading;
    },

    // Add error
    addError(error) {
        globalState.errors.push({
            id: Date.now(),
            message: error.message || error,
            timestamp: new Date().toISOString()
        });

        // Keep only last 10 errors
        if (globalState.errors.length > 10) {
            globalState.errors = globalState.errors.slice(-10);
        }
    },

    // Clear errors
    clearErrors() {
        globalState.errors = [];
    },

    // Set current view
    setCurrentView(view) {
        globalState.currentView = view;
    },

    // Get recent logs
    getRecentLogs(limit = 10) {
        return globalState.logs.slice(0, limit);
    },

    // Clear logs
    clearLogs() {
        globalState.logs = [];
        this.addLog('SYSTEM', 'Logs cleared manually');
        this.saveState();
    },

};