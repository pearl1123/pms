$(document).ready(function () {
    // Get CSRF token
    var csrfToken = $('#csrf_data').val();
    
    console.log('Registration form loaded');
    
    // Simple form submission with basic validation
    $('#frmRegister').on('submit', function(e) {
        console.log('Form submission started');
        
        // Basic required field check
        var fullname = $('#fullname').val();
        var office = $('#office').val();
        var phone = $('#phone_number').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var confirmPass = $('#confirm_pass').val();
        var captcha = $('#captcha').val();
        
        // Check required fields
        if (!fullname || !office || !phone || !email || !password || !confirmPass) {
            alert('Please fill in all required fields');
            e.preventDefault();
            return false;
        }
        
        // Check password match
        if (password !== confirmPass) {
            alert('Passwords do not match');
            e.preventDefault();
            return false;
        }
        
        // Check password length
        if (password.length < 6) {
            alert('Password must be at least 6 characters');
            e.preventDefault();
            return false;
        }
        
        console.log('All basic validation passed - submitting form');
        return true; // Allow form submission
    });
});