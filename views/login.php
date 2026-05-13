<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABC ERP | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            overflow: hidden;
            background-color: #f8fafc;
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #0f4c75, #1a7eb5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 40px;
            color: white;
            box-shadow: inset -10px 0 20px rgba(0, 0, 0, 0.05);
        }

        .right-panel {
            flex: 1;
            background-color: #ffffff;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .logo-container {
            max-width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .logo-image {
            width: 100%;
            max-width: 600px;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
            margin-bottom: 40px;
            border-radius: 12px;
        }

        .welcome-text {
            font-size: 32px; /* Slightly larger than 28px */
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Dark highlight shadow */
        }

        .welcome-subtext {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            max-width: 400px;
        }

        .copyright-text {
            position: absolute;
            bottom: 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            font-weight: 500;
        }

        /* Top Right Header */
        .right-header {
            position: absolute;
            top: 30px;
            left: 40px;
            right: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-logos div {
            font-size: 16px;
            font-weight: 700;
            color: #1a7eb5;
            letter-spacing: -0.3px;
        }

        /* Login Card */
        .login-card {
            background-color: #fff;
            width: 100%;
            max-width: 420px;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .login-title {
            color: #111827;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 35px;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 700; /* Bolder */
            color: #111827; /* Darkest grey/black */
            margin-bottom: 8px;
        }

        .input-field {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            color: #111827;
            transition: all 0.2s;
            background-color: #f9fafb;
            box-sizing: border-box;
        }

        .input-field::placeholder {
            color: #9ca3af;
        }

        .input-field:focus {
            border-color: #1a7eb5;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(26, 126, 181, 0.1);
        }

        .login-btn {
            width: 100%;
            background-color: #1a7eb5;
            color: white;
            border: none;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(26, 126, 181, 0.2);
        }

        .login-btn:hover {
            background-color: #156695;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(26, 126, 181, 0.3);
        }

        /* Mobile Responsive */
        @media (max-width: 900px) {
            body {
                flex-direction: column;
                overflow: auto;
            }

            .left-panel {
                flex: none;
                min-height: 300px;
                padding: 30px 20px;
            }

            .right-panel {
                flex: none;
                min-height: 600px;
                padding: 40px 20px;
                justify-content: flex-start;
            }

            .login-card {
                box-shadow: none;
                border: none;
                padding: 20px;
            }

            .right-header {
                display: none;
            }

            .logo-image {
                max-width: 80%;
            }
        }
    </style>
</head>

<body>
    <div class="left-panel">
        <div class="logo-container">
            <img src="/abc_main.png" alt="ABC Logo" class="logo-image">
            <div class="welcome-text">Anantapur Book Centre ERP</div>
            <div class="welcome-subtext">Manage your institutional inventory, staff registry, and financial analytics
                from one secure, centralized dashboard.</div>
        </div>
        <div class="copyright-text">
            © 2026 Anantapur Book Centre. All Rights Reserved.
        </div>
    </div>

    <div class="right-panel">
        <div class="right-header">
            <div class="header-logos">
                <div>Anantapur Book Centre</div>
            </div>
            <i class="fa-solid fa-book-open-reader text-[#1a7eb5] text-xl"></i>
        </div>

        <div class="login-card">
            <div class="login-title">Account Login</div>

            <form action="index.php?page=login" method="POST">
                <?php if (!empty($login_error)): ?>
                    <div
                        style="color: #b91c1c; font-size: 13px; margin-bottom: 20px; text-align: center; background: #fef2f2; border: 1px solid #fca5a5; padding: 12px; border-radius: 8px; font-weight: 500;">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> <?= htmlspecialchars($login_error) ?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <label class="input-label">Username</label>
                    <input type="text" name="username" class="input-field" placeholder="Enter your username" required>
                </div>

                <div class="input-group">
                    <label class="input-label">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="login-btn">Secure Login</button>
            </form>
        </div>
    </div>
</body>

</html>