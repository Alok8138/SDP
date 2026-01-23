// Main Application Bootstrap
const App = {
    // Initialize application with login
    async init() {
        console.log('Initializing Intern Operations System...');

        // Initialize state
        StateManager.initialize();

        // Check if user is already logged in
        if (!globalState.currentRole) {
            // Show login modal
            this.showLoginModal();
        } else {
            // User is already logged in, proceed normally
            this.initializeApp();
        }

    },

    // Initialize app after login - UPDATED
    initializeApp() {
        console.log('Initializing app for role:', globalState.currentRole);

        // Setup global event delegation (handles dynamic elements)
        this.setupGlobalEventDelegation();

        // Setup initial event listeners
        this.setupEventListeners();

        // Add direct logout button listener (in case delegation doesn't catch it)
        this.setupDirectLogoutListener();

        // Determine initial view based on role
        let startView = globalState.currentView;

        // Force Interns to 'tasks' view if they are not already there
        // This ensures they don't land on Dashboard or see "Access Denied" errors on login
        if (globalState.currentRole === 'INTERN') {
            startView = 'tasks';
        }

        // Render initial view
        this.navigateToView(startView);

        // Update sidebar stats
        Renderer.updateSidebarStats();

        console.log('Application initialized successfully for', globalState.currentRole);
    },

    // Setup direct logout button listener
    setupDirectLogoutListener() {
        // Try to find logout button and attach listener directly
        setTimeout(() => {
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                console.log('Found logout button, attaching direct listener');
                logoutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.logout();
                });
            } else {
                console.warn('Logout button not found on page load');
            }
        }, 500); // Delay to ensure DOM is loaded
    },

    // Show login modal
    showLoginModal() {
        console.log('Showing login modal');
        const modal = document.getElementById('login-modal');
        if (!modal) {
            console.error('Login modal not found');
            // Default to HR if no login modal
            StateManager.setRole('HR');
            this.initializeApp();
            return;
        }

        // Show modal
        modal.style.display = 'flex';

        // Clear any previous error messages
        const errorMsgs = modal.querySelectorAll('.login-error-message');
        errorMsgs.forEach(el => el.textContent = '');

        // Add login button listeners
        const hrBtn = document.getElementById('hr-login-btn');
        const internBtn = document.getElementById('intern-login-btn');
        const toggleButtons = modal.querySelectorAll('.login-toggle-btn');
        const panels = modal.querySelectorAll('.login-panel');

        const setActivePanel = (panelId) => {
            panels.forEach(panel => {
                panel.classList.toggle('active', panel.id === panelId);
            });
            toggleButtons.forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-target') === panelId);
            });
            // Clear errors when switching panels
            errorMsgs.forEach(el => el.textContent = '');
        };

        if (toggleButtons.length) {
            toggleButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.getAttribute('data-target');
                    if (target) setActivePanel(target);
                });
            });
            setActivePanel('hr-login-panel');
        }

        if (hrBtn) {
            // Remove previous listeners to avoid duplicates if any (simple approach)
            const newHrBtn = hrBtn.cloneNode(true);
            hrBtn.parentNode.replaceChild(newHrBtn, hrBtn);
            newHrBtn.addEventListener('click', () => this.loginAsHR());
        }
        if (internBtn) {
            const newInternBtn = internBtn.cloneNode(true);
            internBtn.parentNode.replaceChild(newInternBtn, internBtn);
            newInternBtn.addEventListener('click', () => this.loginAsIntern());
        }
    },

    // Login as HR
    loginAsHR() {
        const idInput = document.getElementById('hr-id');
        const passInput = document.getElementById('hr-password');
        const errorEl = document.getElementById('hr-login-error');

        const hrId = idInput?.value.trim() || '';
        const hrPass = passInput?.value || '';

        // Clear previous error
        if (errorEl) errorEl.textContent = '';

        // Fixed HR credentials
        if (hrId !== 'hr123' || hrPass !== 'hr@1234') {
            if (errorEl) {
                errorEl.textContent = 'Invalid HR ID or password';
            } else {
                Renderer.showError('Invalid HR ID or password');
            }
            return;
        }

        StateManager.setRole('HR');
        this.closeAllModals();
        this.initializeApp();
        Renderer.showSuccess('Logged in as HR Administrator');
    },

    // Login as Intern
    loginAsIntern() {
        const idInput = document.getElementById('intern-id');
        const passInput = document.getElementById('intern-password');
        const errorEl = document.getElementById('intern-login-error');

        const internId = idInput?.value.trim();
        const password = passInput?.value || '';

        // Clear previous error
        if (errorEl) errorEl.textContent = '';

        if (!internId) {
            if (errorEl) errorEl.textContent = 'Please enter your Intern ID';
            return;
        }
        if (!password) {
            if (errorEl) errorEl.textContent = 'Please enter your password';
            return;
        }

        const intern = StateManager.getInternById(internId);

        if (!intern) {
            if (errorEl) errorEl.textContent = 'Invalid Intern ID';
            return;
        }

        // Simple password storage in localStorage: internId -> password
        const storedRaw = localStorage.getItem('internPasswords');
        const stored = storedRaw ? JSON.parse(storedRaw) : {};

        if (!stored[internId]) {
            // First-time password set for this intern
            if (password.length < 4) {
                if (errorEl) errorEl.textContent = 'Password must be at least 4 characters';
                return;
            }
            stored[internId] = password;
            localStorage.setItem('internPasswords', JSON.stringify(stored));
            Renderer.showSuccess('Password set successfully');
        } else if (stored[internId] !== password) {
            if (errorEl) errorEl.textContent = 'Incorrect password';
            return;
        }

        StateManager.setRole('INTERN', internId);
        this.closeAllModals();

        // Ensure clean navigation by NOT setting currentView manually here.
        // Let initializeApp handle the role-based default view.

        this.initializeApp();
        Renderer.showSuccess(`Logged in as ${intern.name}`);
    },

    // Logout function - UPDATED
    logout() {
        console.log('Logout initiated');

        // Create a custom confirmation modal
        const confirmed = confirm('Are you sure you want to logout?');
        if (!confirmed) return;

        // Clear role and intern ID (but keep data like interns/tasks in localStorage)
        StateManager.setRole(null);
        globalState.currentInternId = null;

        // Show logout message
        Renderer.showSuccess('Logged out successfully');

        // Short delay before reload to show success message
        setTimeout(() => {
            location.reload();
        }, 1000);
    },

    // Update setupGlobalEventDelegation to properly catch logout button
    setupGlobalEventDelegation() {
        // Handle clicks on dynamically created elements
        document.addEventListener('click', (e) => {
            console.log('Click event on:', e.target.id, e.target.className);

            // Handle logout button - MOST IMPORTANT FIX
            if (e.target.id === 'logout-btn' ||
                (e.target.closest && e.target.closest('#logout-btn')) ||
                e.target.classList.contains('logout-btn') ||
                (e.target.closest && e.target.closest('.logout-btn'))) {
                console.log('Logout button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.logout();
                return;
            }

            // ... rest of your existing event handlers remain the same ...
        });

        // ... rest of your setupGlobalEventDelegation function ...
    },



    // Setup event listeners for static elements
    setupEventListeners() {
        // Handle static navigation buttons
        const navButtons = document.querySelectorAll('.nav-btn');
        navButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const view = e.target.getAttribute('data-view');
                if (view) {
                    this.navigateToView(view);
                }
            });
        });
    },

    // Setup global event delegation for dynamic elements
    setupGlobalEventDelegation() {
        // Handle clicks on dynamically created elements
        document.addEventListener('click', (e) => {
            // Handle modal close buttons
            if (e.target.classList.contains('close-btn') ||
                e.target.classList.contains('close-modal') ||
                (e.target.closest && e.target.closest('.close-btn')) ||
                (e.target.closest && e.target.closest('.close-modal'))) {
                e.preventDefault();
                this.closeAllModals();
                return;
            }

            // Handle nav buttons (for dynamically added if needed)
            const navBtn = e.target.closest('.nav-btn');
            if (navBtn) {
                const view = navBtn.getAttribute('data-view');
                if (view) {
                    e.preventDefault();
                    this.navigateToView(view);
                }
                return;
            }

            // Handle quick action buttons on dashboard
            if (e.target.id === 'quick-add-intern' || e.target.closest('#quick-add-intern')) {
                e.preventDefault();
                this.navigateToView('interns');
                setTimeout(() => this.showAddInternModal(), 100);
                return;
            }

            if (e.target.id === 'quick-create-task' || e.target.closest('#quick-create-task')) {
                e.preventDefault();
                this.navigateToView('tasks');
                setTimeout(() => this.showAddTaskModal(), 100);
                return;
            }

            if (e.target.id === 'quick-view-active' || e.target.closest('#quick-view-active')) {
                e.preventDefault();
                this.navigateToView('assignments');
                return;
            }

            // Handle add intern button in interns view
            if (e.target.id === 'add-intern-btn' || e.target.closest('#add-intern-btn')) {
                e.preventDefault();
                this.showAddInternModal();
                return;
            }

            // Handle add task button in tasks view
            if (e.target.id === 'add-task-btn' || e.target.closest('#add-task-btn')) {
                e.preventDefault();
                this.showAddTaskModal();
                return;
            }

            // Handle edit task button
            const editTaskBtn = e.target.closest('.edit-task-btn');
            if (editTaskBtn) {
                e.preventDefault();
                const taskId = editTaskBtn.getAttribute('data-task-id');
                if (taskId) {
                    this.showEditTaskModal(taskId);
                }
                return;
            }

            // Handle clear filters button
            if (e.target.id === 'clear-filters' || e.target.closest('#clear-filters')) {
                e.preventDefault();
                this.clearInternFilters();
                return;
            }

            // Handle clear task filters
            if (e.target.id === 'clear-task-filters' || e.target.closest('#clear-task-filters')) {
                e.preventDefault();
                this.clearTaskFilters();
                return;
            }

            // Handle assign button in assignments
            if (e.target.id === 'assign-btn' || e.target.closest('#assign-btn')) {
                e.preventDefault();
                this.assignSelectedTask();
                return;
            }

            // Handle assign modal button (from Task Details)
            const assignModalBtn = e.target.closest('.assign-modal-btn');
            if (assignModalBtn) {
                e.preventDefault();
                const taskId = assignModalBtn.getAttribute('data-task-id');
                if (taskId) {
                    this.navigateToView('assignments');
                    // Wait for view to render then select task
                    setTimeout(() => {
                        const taskSelect = document.getElementById('assignment-task-select');
                        if (taskSelect) {
                            taskSelect.value = taskId;
                            // Trigger change event to update eligible interns
                            const event = new Event('change', { bubbles: true });
                            taskSelect.dispatchEvent(event);
                        }
                    }, 500); // Wait longer for render
                }
                return;
            }

            // Handle unassign button
            const unassignBtn = e.target.closest('.unassign-btn');
            if (unassignBtn) {
                e.preventDefault();
                const taskId = unassignBtn.getAttribute('data-task-id');
                if (taskId) {
                    this.unassignTask(taskId);
                }
                return;
            }

            // Handle update status button
            const updateStatusBtn = e.target.closest('.update-status-btn');
            if (updateStatusBtn) {
                e.preventDefault();
                const taskId = updateStatusBtn.getAttribute('data-task-id');
                const newStatus = updateStatusBtn.getAttribute('data-new-status');
                if (taskId && newStatus) {
                    this.updateTaskStatus(taskId, newStatus);
                }
                return;
            }

            // Handle clear logs button
            if (e.target.id === 'clear-logs' || e.target.closest('#clear-logs')) {
                e.preventDefault();
                this.clearLogs();
                return;
            }

            // Handle export logs button
            if (e.target.id === 'export-logs' || e.target.closest('#export-logs')) {
                e.preventDefault();
                this.exportLogs();
                return;
            }

            // Handle intern action buttons
            const activateBtn = e.target.closest('.activate-btn');
            if (activateBtn) {
                e.preventDefault();
                const internId = activateBtn.getAttribute('data-intern-id');
                if (internId) {
                    this.updateInternStatus(internId, 'ACTIVE');
                }
                return;
            }

            const exitBtn = e.target.closest('.exit-btn');
            if (exitBtn) {
                e.preventDefault();
                const internId = exitBtn.getAttribute('data-intern-id');
                if (internId) {
                    this.updateInternStatus(internId, 'EXITED');
                }
                return;
            }

            const viewTasksBtn = e.target.closest('.view-tasks-btn');
            if (viewTasksBtn) {
                e.preventDefault();
                const internId = viewTasksBtn.getAttribute('data-intern-id');
                if (internId) {
                    this.showInternTasks(internId);
                }
                return;
            }

            // Handle task card clicks
            const taskCard = e.target.closest('.task-card');
            if (taskCard) {
                e.preventDefault();
                const taskId = taskCard.getAttribute('data-task-id');
                if (taskId) {
                    this.selectTask(taskId);
                }
                return;
            }

            // Handle modal background clicks
            if (e.target.classList.contains('modal')) {
                this.closeAllModals();
                return;
            }

            // Handle logout button
            if (e.target.id === 'logout-btn' ||
                (e.target.closest && e.target.closest('#logout-btn')) ||
                e.target.classList.contains('logout-btn') ||
                (e.target.closest && e.target.closest('.logout-btn'))) {
                console.log('Logout button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.logout();
                return;
            }
        });

        // Handle form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'intern-form') {
                e.preventDefault();
                this.handleInternFormSubmit();
                return;
            }

            if (e.target.id === 'task-form') {
                e.preventDefault();
                this.handleTaskFormSubmit();
                return;
            }
        });

        // Handle filter changes
        document.addEventListener('change', (e) => {
            if (e.target.id === 'status-filter' || e.target.id === 'skills-filter') {
                this.filterInterns();
                return;
            }

            if (e.target.id === 'task-status-filter' || e.target.id === 'task-skills-filter') {
                this.filterTasks();
                return;
            }

            if (e.target.id === 'assignment-task-select') {
                const taskId = e.target.value;
                if (taskId) {
                    this.updateEligibleInterns(taskId);
                } else {
                    // Reset to all active interns if no task selected
                    const internSelect = document.getElementById('assignment-intern-select');
                    if (internSelect) {
                        const activeInterns = globalState.interns.filter(intern =>
                            intern.status === 'ACTIVE'
                        );
                        internSelect.innerHTML = `
                            <option value="">Select an intern...</option>
                            ${activeInterns.map(intern => `
                                <option value="${intern.id}">${intern.name} (${intern.id})</option>
                            `).join('')}
                        `;
                    }
                }
                return;
            }
        });

        // Handle search input for tasks
        document.addEventListener('input', (e) => {
            if (e.target.id === 'task-search') {
                // Debounce search
                if (this._searchTimeout) clearTimeout(this._searchTimeout);
                this._searchTimeout = setTimeout(() => {
                    this.filterTasks();
                }, 300);
            }
        });
    },

    navigateToView(viewName) {
        if (!viewName || viewName === globalState.currentView) return;

        // STRICT NAVIGATION GUARD
        // If user is an INTERN, they can ONLY access 'tasks'
        if (globalState.currentRole === 'INTERN' && viewName !== 'tasks') {
            console.warn(`Access denied: Interns cannot access ${viewName}`);
            Renderer.showError('Access Denied: You can only view your tasks.');

            // If we are not already on tasks, go there
            if (globalState.currentView !== 'tasks') {
                this.navigateToView('tasks');
            }
            return;
        }

        console.log(`Navigating to view: ${viewName}`);

        // Update state
        globalState.currentView = viewName;
        StateManager.setCurrentView(viewName);

        // Render view
        Renderer.renderView(viewName);

        // Update navigation
        Renderer.updateNavigation();

        // Update sidebar stats
        Renderer.updateSidebarStats();

        // Add to logs
        StateManager.addLog('NAVIGATION', `Navigated to ${viewName} view`);

        console.log(`View ${viewName} rendered successfully`);
    },

    // Show add intern modal
    showAddInternModal() {
        console.log('Showing add intern modal');
        const modal = document.getElementById('add-intern-modal');
        if (!modal) {
            console.error('Intern modal not found');
            return;
        }

        // Clear form
        const form = document.getElementById('intern-form');
        if (form) {
            form.reset();

            // Clear validation errors
            const errorElements = form.querySelectorAll('.error-message');
            errorElements.forEach(el => {
                el.textContent = '';
            });
        }

        // Show modal
        modal.style.display = 'flex';
        console.log('Intern modal shown');
    },

    // Show add task modal
    showAddTaskModal() {
        console.log('Showing add task modal');
        const modal = document.getElementById('add-task-modal');
        if (!modal) {
            console.error('Task modal not found');
            return;
        }

        // Update modal title
        const titleEl = document.getElementById('task-modal-title');
        if (titleEl) titleEl.innerHTML = '<i class="fas fa-plus-circle"></i> Create New Task';

        // Clear form
        const form = document.getElementById('task-form');
        if (form) {
            form.reset();
            document.getElementById('editing-task-id').value = '';

            // Clear validation errors
            const errorElements = form.querySelectorAll('.error-message');
            errorElements.forEach(el => {
                el.textContent = '';
            });
        }

        // Populate dependencies dropdown
        this.populateDependenciesDropdown();

        // Show modal
        modal.style.display = 'flex';
        console.log('Task modal shown');
    },

    // Show edit task modal
    showEditTaskModal(taskId) {
        console.log('Showing edit task modal for', taskId);
        const task = StateManager.getTaskById(taskId);
        if (!task) return;

        const modal = document.getElementById('add-task-modal');
        if (!modal) return;

        // Update title and hidden field
        const titleEl = document.getElementById('task-modal-title');
        if (titleEl) titleEl.innerHTML = '<i class="fas fa-edit"></i> Edit Task';

        document.getElementById('editing-task-id').value = taskId;

        // Populate form
        document.getElementById('task-title').value = task.title;
        document.getElementById('task-description').value = task.description;
        document.getElementById('task-hours').value = task.estimatedHours;

        // Check skills
        document.querySelectorAll('input[name="task-skill"]').forEach(cb => {
            cb.checked = task.requiredSkills.includes(cb.value);
        });

        // Populate dependencies first then select them
        this.populateDependenciesDropdown(taskId);

        // Select dependencies
        const depSelect = document.getElementById('task-dependencies');
        if (depSelect) {
            Array.from(depSelect.options).forEach(option => {
                option.selected = task.dependencies.includes(option.value);
            });
        }

        modal.style.display = 'flex';
    },

    // Populate dependencies dropdown
    populateDependenciesDropdown(excludeTaskId = null) {
        const dropdown = document.getElementById('task-dependencies');
        if (!dropdown) return;

        // Can't depend on self or done tasks (though done tasks might be validdeps depending on rules, let's keep it simple)
        // Usually you can depend on completed tasks. But avoiding circular means we shouldn't depend on ourselves.
        const availableTasks = globalState.tasks.filter(task => task.id !== excludeTaskId);

        dropdown.innerHTML = availableTasks.map(task => `
            <option value="${task.id}">${task.id}: ${task.title} (${task.status})</option>
        `).join('');
    },

    // Handle task form submission
    async handleTaskFormSubmit() {
        console.log('Handling task form submission...');

        // Show loading
        Renderer.showLoading();

        try {
            const editingId = document.getElementById('editing-task-id').value;

            // Get form values directly
            const title = document.getElementById('task-title').value.trim();
            const description = document.getElementById('task-description').value.trim();
            const hours = document.getElementById('task-hours').value;

            // Get selected skills
            const skillCheckboxes = document.querySelectorAll('input[name="task-skill"]:checked');
            const requiredSkills = Array.from(skillCheckboxes).map(cb => cb.value);

            // Get dependencies
            const dependenciesSelect = document.getElementById('task-dependencies');
            const dependencies = Array.from(dependenciesSelect.selectedOptions).map(option => option.value);

            console.log('Task form data:', { title, description, hours, requiredSkills, dependencies });

            // Basic validation
            if (!title || title.length < 3) {
                throw new Error('Title must be at least 3 characters long');
            }

            if (!description || description.length < 10) {
                throw new Error('Description must be at least 10 characters long');
            }

            if (!hours || isNaN(hours) || hours < 1 || hours > 100) {
                throw new Error('Estimated hours must be between 1 and 100');
            }

            if (requiredSkills.length === 0) {
                throw new Error('At least one required skill is needed');
            }

            // Create task data object
            const taskData = {
                title: title,
                description: description,
                estimatedHours: parseInt(hours),
                requiredSkills: requiredSkills,
                dependencies: dependencies
            };

            let result;
            if (editingId) {
                result = await FakeServer.updateTask(editingId, taskData);
            } else {
                result = await FakeServer.createTask(taskData);
            }

            // Close modal
            this.closeAllModals();

            // Show success
            Renderer.showSuccess(result.message);

            // Refresh tasks view if we're on it
            if (globalState.currentView === 'tasks') {
                this.filterTasks(); // Refresh with current filters
                if (editingId) Renderer.renderTaskDetails(editingId);
            }

            // Update dashboard stats
            Renderer.updateDashboardStats();
            Renderer.updateSidebarStats();

        } catch (error) {
            console.error('Error saving task:', error);
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Filter tasks
    filterTasks() {
        const searchInput = document.getElementById('task-search');
        const statusFilter = document.getElementById('task-status-filter');
        const skillsFilter = document.getElementById('task-skills-filter');

        // Securely select base tasks based on role
        let filteredTasks;
        if (globalState.currentRole === 'INTERN') {
            // Interns only search their assigned tasks
            const internId = StateManager.getCurrentIntern()?.id;
            filteredTasks = internId ? StateManager.getTasksForIntern(internId) : [];
        } else {
            // HR sees all tasks
            filteredTasks = [...globalState.tasks];
        }

        // Apply search
        if (searchInput && searchInput.value) {
            const query = searchInput.value.toLowerCase();
            filteredTasks = filteredTasks.filter(task =>
                task.title.toLowerCase().includes(query) ||
                task.description.toLowerCase().includes(query) ||
                task.id.toLowerCase().includes(query)
            );
        }

        // Apply status filter
        if (statusFilter && statusFilter.value !== 'all') {
            filteredTasks = filteredTasks.filter(task => task.status === statusFilter.value);
        }

        // Apply skills filter
        if (skillsFilter && skillsFilter.selectedOptions.length > 0) {
            const selectedSkills = Array.from(skillsFilter.selectedOptions).map(opt => opt.value);
            filteredTasks = filteredTasks.filter(task =>
                selectedSkills.every(skill => task.requiredSkills.includes(skill))
            );
        }

        Renderer.renderTasksList(filteredTasks);
    },

    // Clear task filters
    clearTaskFilters() {
        const searchInput = document.getElementById('task-search');
        const statusFilter = document.getElementById('task-status-filter');
        const skillsFilter = document.getElementById('task-skills-filter');

        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        if (skillsFilter) {
            Array.from(skillsFilter.options).forEach(option => option.selected = false);
        }

        this.filterTasks();
    },

    // Filter interns
    async filterInterns() {
        const statusFilter = document.getElementById('status-filter');
        const skillsFilter = document.getElementById('skills-filter');

        if (!statusFilter || !skillsFilter) return;

        const status = statusFilter.value;
        const skills = Array.from(skillsFilter.selectedOptions).map(option => option.value);

        // Show loading
        Renderer.showLoading();

        try {
            const result = await FakeServer.fetchData('interns', { status, skills });
            Renderer.renderInternsTable(result.data);
        } catch (error) {
            Renderer.showError('Failed to filter interns');
        } finally {
            Renderer.hideLoading();
        }
    },

    // Clear intern filters
    clearInternFilters() {
        const statusFilter = document.getElementById('status-filter');
        const skillsFilter = document.getElementById('skills-filter');

        if (statusFilter) statusFilter.value = 'all';
        if (skillsFilter) {
            Array.from(skillsFilter.options).forEach(option => option.selected = false);
        }

        Renderer.renderInternsTable();
    },

    // Select task
    selectTask(taskId) {
        // Remove previous selection
        document.querySelectorAll('.task-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Add selection to clicked task
        const selectedCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }

        // Render task details
        Renderer.renderTaskDetails(taskId);
    },

    // Update intern status
    async updateInternStatus(internId, newStatus) {
        const intern = StateManager.getInternById(internId);
        if (!intern) {
            Renderer.showError('Intern not found');
            return;
        }

        // Check if status is actually changing
        if (intern.status === newStatus) {
            Renderer.showError(`Intern is already ${newStatus}`);
            return;
        }

        // Simple validation rules
        if (intern.status === 'EXITED' && newStatus === 'ACTIVE') {
            Renderer.showError('Cannot reactivate an EXITED intern');
            return;
        }

        if (intern.status === 'ONBOARDING' && newStatus === 'EXITED') {
            // Allow ONBOARDING -> EXITED
        } else if (intern.status === 'ONBOARDING' && newStatus !== 'ACTIVE') {
            Renderer.showError('Onboarding interns can only be activated');
            return;
        }

        if (intern.status === 'ACTIVE' && newStatus !== 'EXITED') {
            Renderer.showError('Active interns can only be exited');
            return;
        }

        // Show confirmation
        const confirmed = confirm(`Change ${intern.name}'s status from ${intern.status} to ${newStatus}?`);
        if (!confirmed) return;

        // Show loading
        Renderer.showLoading();

        try {
            // Update status
            const success = StateManager.updateInternStatus(internId, newStatus);

            if (!success) {
                throw new Error('Failed to update status');
            }

            Renderer.showSuccess(`Intern status updated to ${newStatus}`);

            // Refresh the interns view
            if (globalState.currentView === 'interns') {
                Renderer.renderInternsTable();
            }

            // If we're in assignments view and intern became inactive, refresh
            if (globalState.currentView === 'assignments' && newStatus !== 'ACTIVE') {
                Renderer.renderAssignmentsView();
            }

            // Update dashboard stats
            Renderer.updateDashboardStats();
            Renderer.updateSidebarStats();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Show intern tasks
    showInternTasks(internId) {
        const intern = StateManager.getInternById(internId);
        if (!intern) {
            Renderer.showError('Intern not found');
            return;
        }

        const tasks = StateManager.getTasksForIntern(internId);

        if (tasks.length === 0) {
            alert(`${intern.name} has no assigned tasks.`);
            return;
        }

        const taskList = tasks.map(task => `
            • ${task.id}: ${task.title}
              Status: ${task.status}
              Hours: ${task.estimatedHours}
              Skills: ${task.requiredSkills.join(', ')}
        `).join('\n\n');

        alert(`${intern.name}'s Tasks:\n\n${taskList}`);
    },

    // Assign selected task
    async assignSelectedTask() {
        const taskSelect = document.getElementById('assignment-task-select');
        const internSelect = document.getElementById('assignment-intern-select');

        if (!taskSelect || !internSelect) return;

        const taskId = taskSelect.value;
        const internId = internSelect.value;

        if (!taskId || !internId) {
            Renderer.showError('Please select both a task and an intern');
            return;
        }

        // Show loading
        Renderer.showLoading();

        try {
            await FakeServer.assignTask(taskId, internId);
            Renderer.showSuccess('Task assigned successfully!');

            // Refresh assignments view
            Renderer.renderAssignmentsView();

            // Clear selections
            taskSelect.value = '';
            internSelect.value = '';

            // Update dashboard stats
            Renderer.updateDashboardStats();
            Renderer.updateSidebarStats();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Update eligible interns based on selected task
    updateEligibleInterns(taskId) {
        const internSelect = document.getElementById('assignment-intern-select');
        if (!internSelect || !taskId) return;

        const task = StateManager.getTaskById(taskId);
        if (!task) return;

        // Get eligible interns
        const eligibleInterns = RulesEngine.getEligibleInterns(taskId);

        // Update intern dropdown
        if (eligibleInterns.length === 0) {
            internSelect.innerHTML = `
                <option value="">No eligible interns found</option>
            `;
            Renderer.showError('No eligible interns found for this task');
        } else {
            internSelect.innerHTML = `
                <option value="">Select an intern...</option>
                ${eligibleInterns.map(intern => `
                    <option value="${intern.id}">${intern.name} (${intern.id})</option>
                `).join('')}
            `;
        }
    },

    // Assign task from modal
    async assignTask(taskId) {
        const select = document.getElementById('assign-intern-select');
        if (!select) {
            // Try to get select from assignment view
            const assignmentSelect = document.getElementById('assignment-intern-select');
            if (!assignmentSelect || !assignmentSelect.value) {
                Renderer.showError('Please select an intern first');
                return;
            }
            await this.assignSelectedTask();
            return;
        }

        const internId = select.value;
        if (!internId) {
            Renderer.showError('Please select an intern');
            return;
        }

        // Show loading
        Renderer.showLoading();

        try {
            await FakeServer.assignTask(taskId, internId);
            Renderer.showSuccess('Task assigned successfully!');

            // Close all modals
            this.closeAllModals();

            // Refresh views
            if (globalState.currentView === 'tasks') {
                Renderer.renderTasksList();
                Renderer.renderTaskDetails(taskId);
            } else if (globalState.currentView === 'assignments') {
                Renderer.renderAssignmentsView();
            }

            // Update dashboard stats
            Renderer.updateDashboardStats();
            Renderer.updateSidebarStats();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Unassign task
    async unassignTask(taskId) {
        const confirmed = confirm('Unassign this task?');
        if (!confirmed) return;

        // Show loading
        Renderer.showLoading();

        try {
            StateManager.unassignTask(taskId);
            Renderer.showSuccess('Task unassigned successfully');

            // Refresh views
            if (globalState.currentView === 'tasks') {
                Renderer.renderTasksList();
                Renderer.renderTaskDetails(taskId);
            } else if (globalState.currentView === 'assignments') {
                Renderer.renderAssignmentsView();
            }

            // Update dashboard stats
            Renderer.updateDashboardStats();
            Renderer.updateSidebarStats();

        } catch (error) {
            Renderer.showError('Failed to unassign task');
        } finally {
            Renderer.hideLoading();
        }
    },

    // Update task status
    async updateTaskStatus(taskId, newStatus) {
        const task = StateManager.getTaskById(taskId);
        if (!task) return;

        // If logged in as INTERN, only allow status changes on their own tasks
        if (globalState.currentRole === 'INTERN') {
            const currentIntern = StateManager.getCurrentIntern();
            if (!currentIntern || task.assignedTo !== currentIntern.id) {
                Renderer.showError('You can only update status for tasks assigned to you');
                return;
            }
        }

        // Check business rules
        const ruleCheck = RulesEngine.canChangeTaskStatus(taskId, newStatus);

        if (!ruleCheck.allowed) {
            Renderer.showError(ruleCheck.reason);
            return;
        }

        const confirmed = confirm(`Change task status from ${task.status} to ${newStatus}?`);
        if (!confirmed) return;

        // Show loading
        Renderer.showLoading();

        try {
            await FakeServer.updateTaskStatus(taskId, newStatus);
            Renderer.showSuccess('Task status updated successfully');

            // Refresh views
            if (globalState.currentView === 'tasks') {
                if (globalState.currentRole === 'INTERN') {
                    // Intern should only ever see their own tasks
                    Renderer.renderInternTasksView();
                } else {
                    Renderer.renderTasksList();
                }
                Renderer.renderTaskDetails(taskId);
            } else if (globalState.currentView === 'assignments') {
                Renderer.renderAssignmentsView();
            }

            // Update dashboard stats
            Renderer.updateDashboardStats();
            Renderer.updateSidebarStats();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Clear logs
    clearLogs() {
        const confirmed = confirm('Clear all system logs?');
        if (!confirmed) return;

        StateManager.clearLogs();
        Renderer.showSuccess('Logs cleared successfully');

        if (globalState.currentView === 'logs') {
            Renderer.renderLogsTable();
        }
    },

    // Export logs
    exportLogs() {
        const logs = globalState.logs;

        if (logs.length === 0) {
            Renderer.showError('No logs to export');
            return;
        }

        const csvContent = logs.map(log =>
            `${log.timestamp},${log.action},${log.details},${log.metadata?.userId || 'SYSTEM'}`
        ).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `intern-ops-logs-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        Renderer.showSuccess('Logs exported successfully');
    },

    // Close all modals
    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
};

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Debug helpers
window.debugState = () => {
    console.log('=== DEBUG STATE ===');
    console.log('Interns:', globalState.interns);
    console.log('Tasks:', globalState.tasks);
    console.log('Current View:', globalState.currentView);
    console.log('Logs count:', globalState.logs.length);
};

window.testAll = () => {
    console.log('Testing all features...');
    App.navigateToView('dashboard');
    setTimeout(() => App.navigateToView('interns'), 500);
    setTimeout(() => App.navigateToView('tasks'), 1000);
    setTimeout(() => App.navigateToView('assignments'), 1500);
    setTimeout(() => App.navigateToView('logs'), 2000);
    setTimeout(() => App.navigateToView('dashboard'), 2500);
};

window.resetData = () => {
    if (confirm('Reset all data to sample data?')) {
        localStorage.removeItem('internOpsState');
        location.reload();
    }
};

















