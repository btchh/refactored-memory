/**
 * Password Validation Module
 * Provides real-time password strength and match validation
 * Used across user registration and admin creation forms
 */

export class PasswordValidator {
    constructor(options = {}) {
        this.passwordInput = options.passwordInput;
        this.confirmPasswordInput = options.confirmPasswordInput;
        this.strengthBar = options.strengthBar;
        this.strengthText = options.strengthText;
        this.matchText = options.matchText;
        
        this.init();
    }

    init() {
        if (!this.passwordInput) return;

        // Password strength checking
        this.passwordInput.addEventListener('input', () => {
            this.updatePasswordStrength();
            
            // Check password match if confirm field has value
            if (this.confirmPasswordInput && this.confirmPasswordInput.value) {
                this.checkPasswordMatch();
            }
        });

        // Password match checking
        if (this.confirmPasswordInput) {
            this.confirmPasswordInput.addEventListener('input', () => {
                this.checkPasswordMatch();
            });
        }
    }

    updatePasswordStrength() {
        const password = this.passwordInput.value;
        const strength = this.calculatePasswordStrength(password);
        
        if (this.strengthBar && this.strengthText) {
            // Update strength bar
            this.strengthBar.style.width = `${strength.percentage}%`;
            this.strengthBar.className = `h-full transition-all duration-300 ${strength.colorClass}`;
            
            // Update strength text
            this.strengthText.textContent = strength.text;
            this.strengthText.className = `text-xs mt-1 ${strength.textColorClass}`;
        }
    }

    calculatePasswordStrength(password) {
        if (!password) {
            return {
                percentage: 0,
                text: '',
                colorClass: 'bg-gray-300',
                textColorClass: 'text-gray-500'
            };
        }

        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 15;
        
        // Character variety checks
        if (/[a-z]/.test(password)) strength += 15; // lowercase
        if (/[A-Z]/.test(password)) strength += 15; // uppercase
        if (/[0-9]/.test(password)) strength += 15; // numbers
        if (/[^a-zA-Z0-9]/.test(password)) strength += 15; // special chars

        let text, colorClass, textColorClass;
        
        if (strength < 40) {
            text = 'Weak password';
            colorClass = 'bg-red-500';
            textColorClass = 'text-red-500';
        } else if (strength < 70) {
            text = 'Medium password';
            colorClass = 'bg-yellow-500';
            textColorClass = 'text-yellow-600';
        } else {
            text = 'Strong password';
            colorClass = 'bg-green-500';
            textColorClass = 'text-green-600';
        }

        return {
            percentage: strength,
            text,
            colorClass,
            textColorClass
        };
    }

    checkPasswordMatch() {
        if (!this.passwordInput || !this.confirmPasswordInput || !this.matchText) return;

        const password = this.passwordInput.value;
        const confirmPassword = this.confirmPasswordInput.value;

        if (!confirmPassword) {
            this.matchText.textContent = '';
            this.matchText.className = 'text-xs mt-2';
            return;
        }

        if (password === confirmPassword) {
            this.matchText.textContent = '✓ Passwords match';
            this.matchText.className = 'text-xs mt-2 text-green-600';
        } else {
            this.matchText.textContent = '✗ Passwords do not match';
            this.matchText.className = 'text-xs mt-2 text-red-600';
        }
    }
}
