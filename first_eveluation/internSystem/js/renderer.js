// DOM Rendering Logic
const Renderer = {
    // Render current view based on state
    renderView(viewName, data = {}) {
        console.log(`Renderer: Rendering view ${viewName}`);
        
        const container = document.getElementById('view-container');
        if (!container) {
            console.error('View container not found');
            return;
        }
        
        // Clear container
        container.innerHTML = '';
        
        // Get template
        const template = document.getElementById(`${viewName}-template`);
        if (!template) {
            container.innerHTML = '<div class="error">View template not found</div>';
            console.error(`Template ${viewName}-template not found`);
            return;
        }
        
        // Clone and append template
        const content = template.content.cloneNode(true);
        container.appendChild(content);
        
        console.log(`Template ${viewName} cloned and appended`);
        
        // Render view-specific content with a small delay to ensure DOM is ready
        setTimeout(() => {
            switch (viewName) {
                case 'dashboard':
                    console.log('Calling renderDashboard');
                    this.renderDashboard();
                    break;
                case 'interns':
                    console.log('Calling renderInternsView');
                    this.renderInternsView();
                    break;
                case 'tasks':
                    console.log('Calling renderTasksView');
                    this.renderTasksView();
                    break;
                case 'assignments':
                    console.log('Calling renderAssignmentsView');
                    this.renderAssignmentsView();
                    break;
                case 'logs':
                    console.log('Calling renderLogsView');
                    this.renderLogsView();
                    break;
            }
            
            // Update navigation active state
            this.updateNavigation();
            
            // Update role-based visibility
            this.updateRoleBasedVisibility();
        }, 50);
    },
    
    // Render dashboard - FIXED VERSION
    renderDashboard() {
        console.log('Rendering dashboard...');
        
        // Update stats - this should work now since DOM elements exist
        this.updateDashboardStats();
        
        // Render recent logs
        this.renderRecentLogs();
        
        console.log('Dashboard rendered');
    },
    
    // Force update dashboard stats - FIXED VERSION
    updateDashboardStats() {
        console.log('Updating dashboard stats...');
        
        const stats = StateManager.getStats();
        
        // Wait a moment to ensure DOM elements are available
        setTimeout(() => {
            // Safely update each stat element
            const updateElement = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                    console.log(`Updated ${id}: ${value}`);
                } else {
                    console.warn(`Element ${id} not found`);
                }
            };
            
            updateElement('total-interns', stats.totalInterns);
            updateElement('total-tasks', stats.totalTasks);
            updateElement('active-interns', stats.activeInterns);
            updateElement('completed-tasks', stats.completedTasks);
            
            // Also update sidebar
            this.updateSidebarStats();
        }, 10);
    },
    
    // Render recent logs
    renderRecentLogs() {
        setTimeout(() => {
            const container = document.getElementById('recent-logs');
            if (!container) {
                console.warn('Recent logs container not found');
                return;
            }
            
            const logs = StateManager.getRecentLogs(5);
            
            if (logs.length === 0) {
                container.innerHTML = '<p class="placeholder-text">No recent activity</p>';
                return;
            }
            
            container.innerHTML = logs.map(log => `
                <div class="log-item">
                    <div class="log-timestamp">${new Date(log.timestamp).toLocaleString()}</div>
                    <div class="log-action">${log.action}</div>
                    <div class="log-details">${log.details}</div>
                </div>
            `).join('');
        }, 10);
    },
    
    // Render interns view
    renderInternsView() {
        console.log('Rendering interns view...');
        
        // Render interns table with a small delay
        setTimeout(() => {
            this.renderInternsTable();
        }, 10);
        
        console.log('Interns view rendered');
    },
    
    // Render interns table
    renderInternsTable(interns = null) {
        const tbody = document.getElementById('interns-table-body');
        if (!tbody) {
            console.warn('Interns table body not found');
            return;
        }
        
        const internsToRender = interns || globalState.interns;
        
        if (internsToRender.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="placeholder-text">No interns found</td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = internsToRender.map(intern => {
            const taskCount = intern.assignedTasks.length;
            const totalHours = RulesEngine.calculateTotalHours(intern.id);
            
            // Determine available status changes
            let statusActions = '';
            if (intern.status === 'ONBOARDING') {
                statusActions = `
                    <button class="status-btn activate-btn" data-intern-id="${intern.id}">
                        <i class="fas fa-user-check"></i> Activate
                    </button>
                `;
            } else if (intern.status === 'ACTIVE') {
                statusActions = `
                    <button class="status-btn exit-btn" data-intern-id="${intern.id}">
                        <i class="fas fa-sign-out-alt"></i> Exit
                    </button>
                `;
            } else if (intern.status === 'EXITED') {
                statusActions = `
                    <span class="status-disabled">Cannot change</span>
                `;
            }
            
            return `
                <tr>
                    <td><strong>${intern.id}</strong></td>
                    <td>${intern.name}</td>
                    <td>${intern.email}</td>
                    <td>${intern.skills.map(skill => `<span class="skill-tag">${skill}</span>`).join(' ')}</td>
                    <td><span class="status-badge status-${intern.status}">${intern.status}</span></td>
                    <td>
                        <div>Tasks: ${taskCount}</div>
                        <div><small>Hours: ${totalHours}</small></div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            ${statusActions}
                            <button class="view-tasks-btn" data-intern-id="${intern.id}">
                                <i class="fas fa-tasks"></i> View Tasks
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    },
    
    // Render tasks view
    renderTasksView() {
        console.log('Rendering tasks view for role:', globalState.currentRole);
        
        // Render tasks based on role with a small delay
        setTimeout(() => {
            if (globalState.currentRole === 'INTERN') {
                this.renderInternTasksView();
            } else {
                this.renderAllTasksView();
            }
        }, 10);
        
        console.log('Tasks view rendered');
    },
    
    // Render all tasks (HR view)
    renderAllTasksView() {
        this.renderTasksList();
        this.renderTaskDetails();
    },
    
    // Render tasks list
    renderTasksList(tasks = null) {
        const container = document.getElementById('tasks-container');
        if (!container) {
            console.warn('Tasks container not found');
            return;
        }
        
        const tasksToRender = tasks || globalState.tasks;
        
        if (tasksToRender.length === 0) {
            container.innerHTML = '<p class="placeholder-text">No tasks available</p>';
            return;
        }
        
        container.innerHTML = tasksToRender.map(task => {
            const assignedTo = task.assignedTo ? 
                StateManager.getInternById(task.assignedTo)?.name : 'Unassigned';
            
            return `
                <div class="task-card" data-task-id="${task.id}">
                    <div class="task-header">
                        <div class="task-title">${task.title}</div>
                        <span class="task-status task-status-${task.status}">${task.status}</span>
                    </div>
                    <div class="task-description">${task.description.substring(0, 100)}...</div>
                    <div class="task-skills">
                        ${task.requiredSkills.map(skill => `<span class="skill-tag">${skill}</span>`).join(' ')}
                    </div>
                    <div class="task-info">
                        <small><i class="fas fa-clock"></i> ${task.estimatedHours} hours</small>
                        <small><i class="fas fa-user"></i> ${assignedTo}</small>
                    </div>
                </div>
            `;
        }).join('');
    },
    
    // Render intern tasks view (only tasks assigned to that intern)
    renderInternTasksView() {
        const currentIntern = StateManager.getCurrentIntern();
        if (!currentIntern) {
            this.renderTasksList([]);
            this.renderTaskDetails();
            return;
        }
        
        // Only tasks actually assigned to this intern
        const assignedTasks = StateManager.getTasksForIntern(currentIntern.id);
        
        // Render with type indicators
        const container = document.getElementById('tasks-container');
        if (!container) return;
        
        if (assignedTasks.length === 0) {
            container.innerHTML = '<p class="placeholder-text">You have no tasks assigned</p>';
            return;
        }
        
        container.innerHTML = assignedTasks.map(task => {
            return `
                <div class="task-card intern-task" data-task-id="${task.id}">
                    <div class="task-header">
                        <div class="task-title">${task.title}</div>
                        <div>
                            <span class="task-status task-status-${task.status}">${task.status}</span>
                            <span class="task-type-badge">Assigned to you</span>
                        </div>
                    </div>
                    <div class="task-description">${task.description.substring(0, 100)}...</div>
                    <div class="task-skills">
                        ${task.requiredSkills.map(skill => `<span class="skill-tag">${skill}</span>`).join(' ')}
                    </div>
                    <div class="task-info">
                        <small><i class="fas fa-clock"></i> ${task.estimatedHours} hours</small>
                        <small><i class="fas fa-user-check"></i> Assigned to you</small>
                    </div>
                </div>
            `;
        }).join('');
        
        this.renderTaskDetails();
    },
    
    // Update role badge in header
    updateRoleBadge() {
        setTimeout(() => {
            const badge = document.getElementById('current-role-badge');
            if (badge) {
                const role = globalState.currentRole || 'HR';
                badge.textContent = role;
                badge.className = `role-badge role-${role.toLowerCase()}`;
            }
        }, 10);
    },
    
    // Update this in updateSidebarStats to include role badge update
    updateSidebarStats() {
        setTimeout(() => {
            // Update role badge
            this.updateRoleBadge();
            
            // ... rest of existing sidebar stats code ...
        }, 10);
    },


    
    // Render task details
    renderTaskDetails(taskId = null) {
        const container = document.getElementById('task-details-content');
        if (!container) {
            console.warn('Task details container not found');
            return;
        }
        
        if (!taskId) {
            container.innerHTML = '<p class="placeholder-text">Select a task to view details</p>';
            return;
        }
        
        const task = StateManager.getTaskById(taskId);
        if (!task) {
            container.innerHTML = '<p class="error">Task not found</p>';
            return;
        }
        
        const assignedTo = task.assignedTo ? 
            StateManager.getInternById(task.assignedTo) : null;
        
        const dependencies = task.dependencies.map(depId => {
            const depTask = StateManager.getTaskById(depId);
            return depTask ? { id: depId, title: depTask.title, status: depTask.status } : null;
        }).filter(Boolean);
        
        const currentRole = globalState.currentRole || 'HR';
        const isIntern = currentRole === 'INTERN';
        const currentIntern = isIntern ? StateManager.getCurrentIntern() : null;
        const isOwnTask = isIntern && currentIntern && task.assignedTo === currentIntern.id;

        // Build action buttons based on role
        let actionButtonsHtml = '';

        if (!isIntern) {
            // HR – full controls
            actionButtonsHtml = `
                ${task.assignedTo ? `
                    <button class="secondary-btn unassign-btn" data-task-id="${task.id}">
                        <i class="fas fa-unlink"></i> Unassign
                    </button>
                ` : ''}
                
                ${task.status !== 'DONE' ? `
                    <button class="primary-btn update-status-btn" data-task-id="${task.id}" data-new-status="${this.getNextStatus(task.status)}">
                        <i class="fas fa-arrow-right"></i> Mark as ${this.getNextStatus(task.status)}
                    </button>
                ` : ''}
                
                <button class="secondary-btn assign-modal-btn" data-task-id="${task.id}">
                    <i class="fas fa-user-plus"></i> Assign
                </button>
            `;
        } else if (isOwnTask) {
            // Intern – can only progress their own task
            actionButtonsHtml = `
                ${task.status !== 'DONE' ? `
                    <button class="primary-btn update-status-btn" data-task-id="${task.id}" data-new-status="${this.getNextStatus(task.status)}">
                        <i class="fas fa-arrow-right"></i> Mark as ${this.getNextStatus(task.status)}
                    </button>
                ` : `
                    <span class="status-disabled">Task is already completed</span>
                `}
            `;
        } else {
            // Intern viewing a task not assigned to them (should not normally happen)
            actionButtonsHtml = `
                <span class="status-disabled">You can only update tasks assigned to you.</span>
            `;
        }

        container.innerHTML = `
            <div class="task-details-view">
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-heading"></i> Title</div>
                    <div class="detail-value">${task.title}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-align-left"></i> Description</div>
                    <div class="detail-value">${task.description}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-chart-bar"></i> Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-${task.status}">${task.status}</span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-clock"></i> Estimated Hours</div>
                    <div class="detail-value">${task.estimatedHours} hours</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-cogs"></i> Required Skills</div>
                    <div class="detail-value">
                        ${task.requiredSkills.map(skill => `<span class="skill-tag">${skill}</span>`).join(' ')}
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-user"></i> Assigned To</div>
                    <div class="detail-value">
                        ${assignedTo ? 
                            `<a href="#" class="intern-link" data-intern-id="${assignedTo.id}">
                                ${assignedTo.name} (${assignedTo.id})
                            </a>` : 
                            'Unassigned'}
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-link"></i> Dependencies</div>
                    <div class="detail-value">
                        ${dependencies.length === 0 ? 
                            'None' : 
                            dependencies.map(dep => `
                                <div>
                                    <span class="skill-tag">${dep.id}</span> 
                                    ${dep.title} 
                                    <span class="status-badge status-${dep.status}">${dep.status}</span>
                                </div>
                            `).join('')}
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-calendar"></i> Dates</div>
                    <div class="detail-value">
                        <div>Created: ${new Date(task.createdAt).toLocaleDateString()}</div>
                        <div>Updated: ${new Date(task.updatedAt).toLocaleDateString()}</div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    ${actionButtonsHtml}
                </div>
            </div>
        `;
    },
    
    // Helper to get next status
    getNextStatus(currentStatus) {
        const nextStatusMap = {
            'PENDING': 'ASSIGNED',
            'ASSIGNED': 'IN_PROGRESS',
            'IN_PROGRESS': 'DONE',
            'DONE': 'DONE'
        };
        return nextStatusMap[currentStatus] || 'PENDING';
    },
    
    // Render assignments view
    renderAssignmentsView() {
        console.log('Rendering assignments view...');
        
        // Populate task dropdown and render assignments with delay
        setTimeout(() => {
            this.populateAssignmentDropdowns();
            this.renderAssignmentsList();
        }, 10);
        
        console.log('Assignments view rendered');
    },
    
    // Populate assignment dropdowns
    populateAssignmentDropdowns() {
        const taskSelect = document.getElementById('assignment-task-select');
        const internSelect = document.getElementById('assignment-intern-select');
        
        if (!taskSelect || !internSelect) {
            console.warn('Assignment dropdowns not found');
            return;
        }
        
        // Populate tasks - show ALL pending tasks (not assigned)
        const availableTasks = globalState.tasks.filter(task => 
            !task.assignedTo && task.status === 'PENDING'
        );
        
        taskSelect.innerHTML = `
            <option value="">Select a task...</option>
            ${availableTasks.map(task => `
                <option value="${task.id}">${task.id}: ${task.title}</option>
            `).join('')}
        `;
        
        // Populate interns - show ALL ACTIVE interns initially
        const activeInterns = globalState.interns.filter(intern => 
            intern.status === 'ACTIVE'
        );
        
        internSelect.innerHTML = `
            <option value="">Select an intern...</option>
            ${activeInterns.map(intern => `
                <option value="${intern.id}">${intern.name} (${intern.id})</option>
            `).join('')}
        `;
    },
    
    // Render assignments list
    renderAssignmentsList() {
        const container = document.getElementById('assignments-container');
        if (!container) {
            console.warn('Assignments container not found');
            return;
        }
        
        const assignments = StateManager.getActiveAssignments();
        
        if (assignments.length === 0) {
            container.innerHTML = '<p class="placeholder-text">No active assignments</p>';
            return;
        }
        
        container.innerHTML = assignments.map(assignment => {
            const intern = StateManager.getInternById(assignment.assignedTo);
            
            return `
                <div class="assignment-card">
                    <div class="assignment-header">
                        <div>
                            <strong>${assignment.id}: ${assignment.title}</strong>
                            <div class="task-status task-status-${assignment.status}">${assignment.status}</div>
                        </div>
                        <div class="assignment-actions">
                            <button class="secondary-btn small-btn unassign-btn" data-task-id="${assignment.id}">
                                <i class="fas fa-unlink"></i>
                            </button>
                            <button class="secondary-btn small-btn update-status-btn" data-task-id="${assignment.id}" data-new-status="${this.getNextStatus(assignment.status)}">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="assignment-info">
                        <div><i class="fas fa-user"></i> Assigned to: ${intern?.name || 'Unknown'}</div>
                        <div><i class="fas fa-clock"></i> Hours: ${assignment.estimatedHours}</div>
                        <div><i class="fas fa-cogs"></i> Skills: ${assignment.requiredSkills.join(', ')}</div>
                    </div>
                </div>
            `;
        }).join('');
    },
    
    // Render logs view
    renderLogsView() {
        console.log('Rendering logs view...');
        
        // Render logs table with delay
        setTimeout(() => {
            this.renderLogsTable();
        }, 10);
        
        console.log('Logs view rendered');
    },
    
    // Render logs table
    renderLogsTable() {
        const tbody = document.getElementById('logs-table-body');
        if (!tbody) {
            console.warn('Logs table body not found');
            return;
        }
        
        const logs = globalState.logs;
        
        if (logs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="placeholder-text">No logs available</td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = logs.map(log => `
            <tr>
                <td>${new Date(log.timestamp).toLocaleString()}</td>
                <td><strong>${log.action}</strong></td>
                <td>${log.details}</td>
                <td>${log.metadata?.userId || 'SYSTEM'}</td>
            </tr>
        `).join('');
    },
    
    // Update navigation active state
    updateNavigation() {
        setTimeout(() => {
            const navButtons = document.querySelectorAll('.nav-btn');
            const currentRole = globalState.currentRole || 'HR';

            navButtons.forEach(button => {
                const view = button.getAttribute('data-view');

                // Interns should only see their assigned tasks view
                if (currentRole === 'INTERN') {
                    if (view === 'tasks') {
                        button.style.display = 'inline-flex';
                    } else {
                        button.style.display = 'none';
                    }
                } else {
                    // HR can see all navigation
                    button.style.display = 'inline-flex';
                }

                // Active state
                if (view === globalState.currentView) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
        }, 10);
    },
    
    // Update role-based visibility (currently handled mostly in updateNavigation and per-view renderers)
    updateRoleBasedVisibility() {
        // Kept for compatibility; additional per-view tweaks can be added here if needed
        setTimeout(() => {
            const currentRole = globalState.currentRole || 'HR';

            if (currentRole === 'INTERN' && globalState.currentView === 'tasks') {
                // Hide "Create New Task" button for interns
                const addTaskBtn = document.getElementById('add-task-btn');
                if (addTaskBtn) {
                    addTaskBtn.style.display = 'none';
                }

                // Hide any assign buttons
                document.querySelectorAll('.assign-modal-btn').forEach(btn => {
                    btn.style.display = 'none';
                });
            }
        }, 10);
    },
    
    // Show loading overlay
    showLoading() {
        const loadingEl = document.getElementById('loading');
        if (loadingEl) {
            loadingEl.style.display = 'flex';
        }
    },
    
    // Hide loading overlay
    hideLoading() {
        const loadingEl = document.getElementById('loading');
        if (loadingEl) {
            loadingEl.style.display = 'none';
        }
    },
    
    // Show error message
    showError(message, duration = 5000) {
        const container = document.getElementById('error-container');
        if (!container) return;
        
        container.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </div>
        `;
        container.style.display = 'block';
        
        if (duration > 0) {
            setTimeout(() => {
                container.style.display = 'none';
            }, duration);
        }
    },
    
    // Hide error message
    hideError() {
        const container = document.getElementById('error-container');
        if (container) {
            container.style.display = 'none';
        }
    },
    
    // Show success message
    showSuccess(message, duration = 3000) {
        // Create temporary success message
        const successEl = document.createElement('div');
        successEl.className = 'success-message';
        successEl.innerHTML = `
            <i class="fas fa-check-circle"></i> ${message}
        `;
        successEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 10000;
        `;
        
        document.body.appendChild(successEl);
        
        setTimeout(() => {
            if (successEl.parentNode) {
                successEl.parentNode.removeChild(successEl);
            }
        }, duration);
    },
    
    // Update sidebar stats
    updateSidebarStats() {
        setTimeout(() => {
            const statsContent = document.getElementById('stats-content');
            const statusContent = document.getElementById('status-content');
            
            if (!statsContent || !statusContent) {
                console.warn('Sidebar stats containers not found');
                return;
            }
            
            const stats = StateManager.getStats();
            const currentRole = globalState.currentRole || 'HR';
            
            statsContent.innerHTML = `
                <div>Interns: ${stats.totalInterns}</div>
                <div>Active: ${stats.activeInterns}</div>
                <div>Tasks: ${stats.totalTasks}</div>
                <div>Completed: ${stats.completedTasks}</div>
            `;
            
            const activeAssignments = StateManager.getActiveAssignments().length;
            const pendingTasks = globalState.tasks.filter(t => t.status === 'PENDING').length;
            
            statusContent.innerHTML = `
                <div><i class="fas fa-user-tag"></i> Role: ${currentRole}</div>
                <div><i class="fas fa-circle" style="color: #28a745"></i> System: Active</div>
                <div><i class="fas fa-tasks"></i> Active: ${activeAssignments} assignments</div>
                <div><i class="fas fa-clock"></i> Pending: ${pendingTasks} tasks</div>
            `;
        }, 10);
    }
};