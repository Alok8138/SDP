// Form and Data Validators
const Validators = {
    // Validate email format
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return {
            isValid: emailRegex.test(email),
            message: emailRegex.test(email) ? '' : 'Invalid email format'
        };
    },
    
    // Validate name
    validateName(name) {
        if (!name || name.trim().length === 0) {
            return { isValid: false, message: 'Name is required' };
        }
        
        if (name.trim().length < 2) {
            return { isValid: false, message: 'Name must be at least 2 characters' };
        }
        
        if (name.trim().length > 50) {
            return { isValid: false, message: 'Name must be less than 50 characters' };
        }
        
        return { isValid: true, message: '' };
    },
    
    // Validate skills selection
    validateSkills(skills) {
        if (!skills || skills.length === 0) {
            return { isValid: false, message: 'At least one skill is required' };
        }
        
        const validSkills = ['JavaScript', 'React', 'HTML', 'CSS', 'Node.js', 'Git'];
        const invalidSkills = skills.filter(skill => !validSkills.includes(skill));
        
        if (invalidSkills.length > 0) {
            return { 
                isValid: false, 
                message: `Invalid skills: ${invalidSkills.join(', ')}` 
            };
        }
        
        return { isValid: true, message: '' };
    },
    
    // Validate task title
    validateTaskTitle(title) {
        if (!title || title.trim().length === 0) {
            return { isValid: false, message: 'Title is required' };
        }
        
        if (title.trim().length < 3) {
            return { isValid: false, message: 'Title must be at least 3 characters' };
        }
        
        if (title.trim().length > 100) {
            return { isValid: false, message: 'Title must be less than 100 characters' };
        }
        
        return { isValid: true, message: '' };
    },
    
    // Validate task description
    validateTaskDescription(description) {
        if (!description || description.trim().length === 0) {
            return { isValid: false, message: 'Description is required' };
        }
        
        if (description.trim().length < 10) {
            return { isValid: false, message: 'Description must be at least 10 characters' };
        }
        
        if (description.trim().length > 1000) {
            return { isValid: false, message: 'Description must be less than 1000 characters' };
        }
        
        return { isValid: true, message: '' };
    },
    
    // Validate estimated hours
    validateEstimatedHours(hours) {
        if (!hours || hours === '') {
            return { isValid: false, message: 'Estimated hours are required' };
        }
        
        const hoursNum = parseInt(hours);
        
        if (isNaN(hoursNum)) {
            return { isValid: false, message: 'Must be a valid number' };
        }
        
        if (hoursNum < 1) {
            return { isValid: false, message: 'Must be at least 1 hour' };
        }
        
        if (hoursNum > 100) {
            return { isValid: false, message: 'Cannot exceed 100 hours' };
        }
        
        return { isValid: true, message: '' };
    },
    
    // Validate status selection
    validateStatus(status) {
        const validStatuses = ['ONBOARDING', 'ACTIVE', 'EXITED'];
        
        return {
            isValid: validStatuses.includes(status),
            message: validStatuses.includes(status) ? '' : 'Invalid status'
        };
    },
    
    // Validate intern form
    validateInternForm(formData) {
        const errors = {};
        
        // Validate name
        const nameValidation = this.validateName(formData.name);
        if (!nameValidation.isValid) {
            errors.name = nameValidation.message;
        }
        
        // Validate email
        const emailValidation = this.validateEmail(formData.email);
        if (!emailValidation.isValid) {
            errors.email = emailValidation.message;
        }
        
        // Validate skills
        const skillsValidation = this.validateSkills(formData.skills);
        if (!skillsValidation.isValid) {
            errors.skills = skillsValidation.message;
        }
        
        // Validate status
        if (formData.status) {
            const statusValidation = this.validateStatus(formData.status);
            if (!statusValidation.isValid) {
                errors.status = statusValidation.message;
            }
        }
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    },
    
    // Validate task form
    validateTaskForm(formData) {
        const errors = {};
        
        // Validate title
        const titleValidation = this.validateTaskTitle(formData.title);
        if (!titleValidation.isValid) {
            errors.title = titleValidation.message;
        }
        
        // Validate description
        const descValidation = this.validateTaskDescription(formData.description);
        if (!descValidation.isValid) {
            errors.description = descValidation.message;
        }
        
        // Validate hours
        const hoursValidation = this.validateEstimatedHours(formData.estimatedHours);
        if (!hoursValidation.isValid) {
            errors.estimatedHours = hoursValidation.message;
        }
        
        // Validate skills
        const skillsValidation = this.validateSkills(formData.requiredSkills);
        if (!skillsValidation.isValid) {
            errors.requiredSkills = skillsValidation.message;
        }
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    },
    
    // Real-time form validation
    setupRealTimeValidation(formId, fieldValidators) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        Object.entries(fieldValidators).forEach(([fieldId, validator]) => {
            const field = form.querySelector(`#${fieldId}`);
            const errorElement = form.querySelector(`#${fieldId}-error`);
            
            if (field && errorElement) {
                field.addEventListener('blur', () => {
                    const value = field.type === 'checkbox' ? 
                        Array.from(form.querySelectorAll(`input[name="${field.name}"]:checked`)).map(cb => cb.value) :
                        field.value;
                    
                    const validation = validator(value);
                    errorElement.textContent = validation.message;
                    
                    if (validation.isValid) {
                        field.classList.remove('invalid');
                        field.classList.add('valid');
                    } else {
                        field.classList.remove('valid');
                        field.classList.add('invalid');
                    }
                });
            }
        });
    },
    
    // Validate all fields in a form
    validateForm(formElement) {
        const formData = new FormData(formElement);
        const data = {};
        const errors = {};
        
        // Convert form data to object
        for (const [key, value] of formData.entries()) {
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        
        // Get checkboxes separately
        const checkboxes = formElement.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            const name = checkbox.name;
            if (checkbox.checked) {
                if (!data[name]) {
                    data[name] = [];
                }
                if (!Array.isArray(data[name])) {
                    data[name] = [data[name]];
                }
                if (!data[name].includes(checkbox.value)) {
                    data[name].push(checkbox.value);
                }
            }
        });
        
        // Determine which validator to use based on form ID
        if (formElement.id === 'intern-form') {
            const validation = this.validateInternForm(data);
            return {
                ...validation,
                data
            };
        } else if (formElement.id === 'task-form') {
            const validation = this.validateTaskForm(data);
            return {
                ...validation,
                data
            };
        }
        
        return {
            isValid: false,
            errors: { form: 'Unknown form type' },
            data
        };
    },
    
    // Clear validation errors
    clearValidationErrors(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        const errorElements = form.querySelectorAll('.error-message');
        errorElements.forEach(el => {
            el.textContent = '';
        });
        
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            field.classList.remove('invalid', 'valid');
        });
    }
};