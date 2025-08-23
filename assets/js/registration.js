// Registration form JavaScript with security features
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submit-btn');
    const addMemberBtn = document.getElementById('add-member');
    const teamMembersContainer = document.getElementById('team-members');
    const refreshCaptchaBtn = document.getElementById('refresh-captcha');
    const captchaImage = document.getElementById('captcha-image');
    
    let memberCount = 1;
    let isSubmitting = false;
    
    // Initialize
    init();
    
    function init() {
        setupEventListeners();
        setupFileValidation();
        setupFormValidation();
        setupCaptchaRefresh();
        preventMultipleSubmissions();
    }
    
    function setupEventListeners() {
        // Add team member
        addMemberBtn.addEventListener('click', addTeamMember);
        
        // Form submission
        form.addEventListener('submit', handleFormSubmission);
        
        // Real-time validation
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearError);
        });
        
        // PRC license validation
        const prcLicense = document.getElementById('prc_license');
        if (prcLicense) {
            prcLicense.addEventListener('input', validatePRCLicense);
        }
        
        // Date validation
        const regDate = document.getElementById('prc_registration_date');
        const expDate = document.getElementById('prc_expiration_date');
        if (regDate) regDate.addEventListener('change', validateDates);
        if (expDate) expDate.addEventListener('change', validateDates);
    }
    
    function addTeamMember() {
        memberCount++;
        
        if (memberCount > 10) {
            showError(addMemberBtn, 'Maximum of 10 team members allowed.');
            return;
        }
        
        const memberHtml = `
            <div class="team-member fade-in" data-member="${memberCount}">
                <button type="button" class="remove-member" onclick="removeMember(this)" title="Remove Member">
                    <i data-feather="x"></i>
                </button>
                <h3>Team Member ${memberCount}</h3>
                <div class="form-group">
                    <label for="member_${memberCount}_name" class="required">Full Name</label>
                    <input type="text" id="member_${memberCount}_name" name="members[${memberCount}][name]" 
                           required maxlength="255">
                    <div class="error-message" id="member_${memberCount}_name-error"></div>
                </div>
                <div class="form-group">
                    <label for="member_${memberCount}_proof" class="required">Proof of Enrollment</label>
                    <input type="file" id="member_${memberCount}_proof" name="members[${memberCount}][proof]" 
                           required accept=".pdf,.jpg,.jpeg,.png" data-max-size="5242880">
                    <small>Accepted formats: PDF, JPG, PNG (Max: 5MB)</small>
                    <div class="error-message" id="member_${memberCount}_proof-error"></div>
                </div>
            </div>
        `;
        
        teamMembersContainer.insertAdjacentHTML('beforeend', memberHtml);
        
        // Re-initialize Feather icons for new content
        feather.replace();
        
        // Add validation to new fields
        const newNameInput = document.getElementById(`member_${memberCount}_name`);
        const newFileInput = document.getElementById(`member_${memberCount}_proof`);
        
        newNameInput.addEventListener('blur', validateField);
        newNameInput.addEventListener('input', clearError);
        newFileInput.addEventListener('change', validateFileInput);
        
        // Update button text
        updateAddButtonText();
    }
    
    function updateAddButtonText() {
        const members = teamMembersContainer.querySelectorAll('.team-member').length;
        addMemberBtn.innerHTML = `<i data-feather="plus"></i> Add Team Member (${members}/10)`;
        
        // Re-initialize Feather icons
        feather.replace();
        
        if (members >= 10) {
            addMemberBtn.style.display = 'none';
        }
    }
    
    window.removeMember = function(button) {
        const memberDiv = button.closest('.team-member');
        memberDiv.remove();
        updateAddButtonText();
        addMemberBtn.style.display = 'inline-flex';
        renumberMembers();
    };
    
    function renumberMembers() {
        const members = teamMembersContainer.querySelectorAll('.team-member');
        members.forEach((member, index) => {
            const newNumber = index + 1;
            member.dataset.member = newNumber;
            member.querySelector('h3').textContent = `Team Member ${newNumber}`;
            
            // Update input names and IDs
            const nameInput = member.querySelector('input[type="text"]');
            const fileInput = member.querySelector('input[type="file"]');
            const nameError = member.querySelector('.error-message');
            
            nameInput.id = `member_${newNumber}_name`;
            nameInput.name = `members[${newNumber}][name]`;
            fileInput.id = `member_${newNumber}_proof`;
            fileInput.name = `members[${newNumber}][proof]`;
            nameError.id = `member_${newNumber}_name-error`;
            
            // Update labels
            member.querySelector('label[for^="member_"]').setAttribute('for', `member_${newNumber}_name`);
            member.querySelectorAll('label')[1].setAttribute('for', `member_${newNumber}_proof`);
        });
        
        memberCount = members.length;
        updateAddButtonText();
    }
    
    function setupFileValidation() {
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', validateFileInput);
        });
    }
    
    function validateFileInput(event) {
        const input = event.target;
        const file = input.files[0];
        const maxSize = parseInt(input.dataset.maxSize) || 5242880; // 5MB default
        const allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        
        clearError(event);
        
        if (!file) return;
        
        // Check file size
        if (file.size > maxSize) {
            showError(input, 'File size must be less than 5MB.');
            input.value = '';
            return;
        }
        
        // Check file type
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExtension)) {
            showError(input, 'Only PDF, JPG, and PNG files are allowed.');
            input.value = '';
            return;
        }
        
        // Validate file content (basic check)
        const reader = new FileReader();
        reader.onload = function(e) {
            const arrayBuffer = e.target.result;
            const uint8Array = new Uint8Array(arrayBuffer);
            
            // Check PDF signature
            if (fileExtension === 'pdf') {
                const pdfSignature = [0x25, 0x50, 0x44, 0x46]; // %PDF
                for (let i = 0; i < pdfSignature.length; i++) {
                    if (uint8Array[i] !== pdfSignature[i]) {
                        showError(input, 'Invalid PDF file.');
                        input.value = '';
                        return;
                    }
                }
            }
            
            // Check JPEG signature
            if (fileExtension === 'jpg' || fileExtension === 'jpeg') {
                if (uint8Array[0] !== 0xFF || uint8Array[1] !== 0xD8) {
                    showError(input, 'Invalid JPEG file.');
                    input.value = '';
                    return;
                }
            }
            
            // Check PNG signature
            if (fileExtension === 'png') {
                const pngSignature = [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A];
                for (let i = 0; i < pngSignature.length; i++) {
                    if (uint8Array[i] !== pngSignature[i]) {
                        showError(input, 'Invalid PNG file.');
                        input.value = '';
                        return;
                    }
                }
            }
        };
        
        reader.readAsArrayBuffer(file.slice(0, 100)); // Read first 100 bytes
    }
    
    function setupFormValidation() {
        // Institution validation
        const institution = document.getElementById('institution');
        if (institution) {
            institution.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z0-9\s\-\.&,()]/g, '');
            });
        }
        
        // Coach name validation
        const coachName = document.getElementById('coach_name');
        if (coachName) {
            coachName.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z\s\-\.]/g, '');
            });
        }
        
        // Team member name validation
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[name]"]')) {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s\-\.]/g, '');
            }
        });
    }
    
    function validatePRCLicense() {
        const input = document.getElementById('prc_license');
        const value = input.value.replace(/\D/g, ''); // Remove non-digits
        input.value = value;
        
        clearError({ target: input });
        
        if (value && (value.length < 7 || value.length > 10)) {
            showError(input, 'PRC license must be 7-10 digits.');
        }
    }
    
    function validateDates() {
        const regDate = document.getElementById('prc_registration_date');
        const expDate = document.getElementById('prc_expiration_date');
        
        if (!regDate.value || !expDate.value) return;
        
        const registrationDate = new Date(regDate.value);
        const expirationDate = new Date(expDate.value);
        const today = new Date();
        
        clearError({ target: regDate });
        clearError({ target: expDate });
        
        if (registrationDate > today) {
            showError(regDate, 'Registration date cannot be in the future.');
        }
        
        if (expirationDate <= today) {
            showError(expDate, 'License has expired. Please provide a valid license.');
        }
        
        if (expirationDate <= registrationDate) {
            showError(expDate, 'Expiration date must be after registration date.');
        }
    }
    
    function validateField(event) {
        const input = event.target;
        const value = input.value.trim();
        
        clearError(event);
        
        if (input.hasAttribute('required') && !value) {
            showError(input, 'This field is required.');
            return false;
        }
        
        if (input.hasAttribute('maxlength') && value.length > input.maxLength) {
            showError(input, `Maximum ${input.maxLength} characters allowed.`);
            return false;
        }
        
        return true;
    }
    
    function showError(input, message) {
        const errorDiv = document.getElementById(input.id + '-error');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }
        input.style.borderColor = '#e74c3c';
    }
    
    function clearError(event) {
        const input = event.target;
        const errorDiv = document.getElementById(input.id + '-error');
        if (errorDiv) {
            errorDiv.classList.remove('show');
        }
        input.style.borderColor = '#ddd';
    }
    
    function setupCaptchaRefresh() {
        if (refreshCaptchaBtn && captchaImage) {
            refreshCaptchaBtn.addEventListener('click', function() {
                this.classList.add('loading');
                captchaImage.src = 'captcha.php?' + Date.now();
                
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 500);
            });
        }
    }
    
    function preventMultipleSubmissions() {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    function handleFormSubmission(event) {
        event.preventDefault();
        
        if (isSubmitting) return false;
        
        // Validate all fields
        const inputs = form.querySelectorAll('input[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField({ target: input })) {
                isValid = false;
            }
        });
        
        // Validate file uploads
        const fileInputs = form.querySelectorAll('input[type="file"][required]');
        fileInputs.forEach(input => {
            if (!input.files || !input.files[0]) {
                showError(input, 'This file is required.');
                isValid = false;
            }
        });
        
        // Validate captcha
        const captcha = document.getElementById('captcha');
        if (captcha && !captcha.value.trim()) {
            showError(captcha, 'Security code is required.');
            isValid = false;
        }
        
        if (!isValid) {
            // Scroll to first error
            const firstError = form.querySelector('.error-message.show');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
        
        // Show loading state
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-feather="loader"></i> Submitting...';
        submitBtn.classList.add('loading');
        
        // Re-initialize Feather icons
        feather.replace();
        
        // Submit form
        setTimeout(() => {
            form.submit();
        }, 1000);
        
        return true;
    }
    
    // Security: Disable form if page is accessed via iframe
    if (window.top !== window.self) {
        document.body.innerHTML = '<h1>Access Denied</h1><p>This form cannot be loaded in a frame.</p>';
    }
    
    // Security: Disable right-click context menu on sensitive areas
    form.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Security: Clear form data on page unload
    window.addEventListener('beforeunload', function() {
        form.reset();
    });
    
    // Security: Detect potential XSS attempts
    document.addEventListener('input', function(e) {
        const value = e.target.value;
        const dangerousPatterns = [
            /<script/i,
            /javascript:/i,
            /on\w+\s*=/i,
            /<iframe/i,
            /<object/i,
            /<embed/i
        ];
        
        dangerousPatterns.forEach(pattern => {
            if (pattern.test(value)) {
                e.target.value = value.replace(pattern, '');
                showError(e.target, 'Invalid characters detected and removed.');
            }
        });
    });
    
    // Auto-save form data to localStorage (except sensitive fields)
    function autoSave() {
        const formData = {};
        const inputs = form.querySelectorAll('input:not([type="file"]):not([type="password"]):not([name="csrf_token"]):not([name="captcha"])');
        
        inputs.forEach(input => {
            if (input.name && input.value) {
                formData[input.name] = input.value;
            }
        });
        
        localStorage.setItem('registration_form_data', JSON.stringify(formData));
    }
    
    // Restore form data from localStorage
    function restoreFormData() {
        const savedData = localStorage.getItem('registration_form_data');
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                Object.keys(formData).forEach(name => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input && input.type !== 'file') {
                        input.value = formData[name];
                    }
                });
            } catch (e) {
                console.warn('Failed to restore form data:', e);
            }
        }
    }
    
    // Auto-save every 30 seconds
    setInterval(autoSave, 30000);
    
    // Restore data on page load
    restoreFormData();
    
    // Clear saved data on successful submission
    if (window.location.search.includes('success=1')) {
        localStorage.removeItem('registration_form_data');
    }
});
