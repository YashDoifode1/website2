<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Secure Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --error-color: #e63946;
            --success-color: #2a9d8f;
            --warning-color: #f4a261;
            --box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .reset-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .reset-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .reset-left::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            top: -50px;
            left: -50px;
        }

        .reset-left::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            bottom: -50px;
            right: -50px;
        }

        .reset-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 28px;
            margin-right: 10px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
        }

        .welcome-text {
            margin-bottom: 30px;
        }

        .welcome-text h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome-text p {
            opacity: 0.9;
        }

        .features {
            margin-top: 30px;
        }

        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .feature i {
            margin-right: 10px;
            font-size: 18px;
        }

        .reset-form {
            width: 100%;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 28px;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .form-header p {
            color: var(--dark-gray);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
        }

        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid var(--medium-gray);
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit i {
            margin-right: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-back {
            width: 100%;
            padding: 12px;
            background: var(--light-gray);
            color: var(--text-color);
            border: 1px solid var(--medium-gray);
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: var(--medium-gray);
        }

        .security-notice {
            margin-top: 25px;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
        }

        .security-notice i {
            color: var(--success-color);
            margin-right: 10px;
            font-size: 16px;
            margin-top: 2px;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            display: none;
        }

        .message.error {
            background: rgba(230, 57, 70, 0.1);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
            display: block;
        }

        .message.success {
            background: rgba(42, 157, 143, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
            display: block;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .reset-container {
                flex-direction: column;
                max-width: 500px;
            }

            .reset-left {
                padding: 30px;
                border-radius: 20px 20px 0 0;
            }

            .reset-right {
                padding: 30px;
            }

            .welcome-text h1 {
                font-size: 26px;
            }

            .form-header h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .reset-container {
                min-height: auto;
            }

            .reset-left, .reset-right {
                padding: 25px;
            }

            .welcome-text h1 {
                font-size: 22px;
            }

            .form-header h2 {
                font-size: 22px;
            }
        }

        /* Loading animation */
        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-left">
            <div class="logo">
                <i class="fas fa-shield-alt logo-icon"></i>
                <span class="logo-text">SecureAdmin</span>
            </div>
            <div class="welcome-text">
                <h1>Reset Your Password</h1>
                <p>Enter your email address and we'll send you a secure link to reset your password.</p>
            </div>
            <div class="features">
                <div class="feature">
                    <i class="fas fa-lock"></i>
                    <span>Secure token-based reset system</span>
                </div>
                <div class="feature">
                    <i class="fas fa-clock"></i>
                    <span>Reset link expires in 1 hour</span>
                </div>
                <div class="feature">
                    <i class="fas fa-envelope"></i>
                    <span>Check your spam folder if you don't see the email</span>
                </div>
            </div>
        </div>
        <div class="reset-right">
            <div class="form-header">
                <h2>Password Recovery</h2>
                <p>Enter your registered email address to receive a reset link</p>
            </div>
            
            <!-- Messages -->
            <?php if ($error): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form class="reset-form" method="POST" id="resetForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your registered email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit" id="submitButton">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            <?php endif; ?>
            
            <a href="login.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
            
            <div class="security-notice">
                <i class="fas fa-info-circle"></i>
                <div>For security reasons, the password reset link will expire in 1 hour. If you didn't request this reset, please ignore this email.</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetForm = document.getElementById('resetForm');
            const submitButton = document.getElementById('submitButton');
            
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    // Basic client-side validation
                    const email = document.getElementById('email').value.trim();
                    
                    if (!email) {
                        e.preventDefault();
                        alert('Please enter your email address');
                        return;
                    }
                    
                    // Email format validation
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        alert('Please enter a valid email address');
                        return;
                    }
                    
                    // Show loading state
                    submitButton.classList.add('loading');
                    submitButton.innerHTML = '<i class="fas fa-spinner"></i> Sending...';
                });
            }
        });
    </script>
</body>
</html>