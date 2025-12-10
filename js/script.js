document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('form[action*="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = this.querySelector('input[name="password"]');
            const confirmPassword = this.querySelector('input[name="confirm_password"]');
            
            if (password.value.length < 6) {
                alert('Пароль должен содержать минимум 6 символов');
                e.preventDefault();
                return false;
            }
            
            if (password.value !== confirmPassword.value) {
                alert('Пароли не совпадают');
                e.preventDefault();
                return false;
            }
        });
    }
    
    const recordForm = document.querySelector('form[action=""]');
    if (recordForm && !registerForm) {
        recordForm.addEventListener('submit', function(e) {
            const date = this.querySelector('input[type="date"]');
            const type = this.querySelector('select[name="type"]');
            
            if (!date.value) {
                alert('Пожалуйста, выберите дату');
                e.preventDefault();
                return false;
            }
            
            if (!type.value) {
                alert('Пожалуйста, выберите тип отсутствия');
                e.preventDefault();
                return false;
            }
        });
    }
    
    const adminButtons = document.querySelectorAll('button[name="action"]');
    adminButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const action = this.value;
            const message = action === 'approve' 
                ? 'Вы уверены, что хотите подтвердить эту запись?' 
                : 'Вы уверены, что хотите отклонить эту запись?';
            
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});