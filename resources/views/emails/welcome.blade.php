<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Splitwise</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f6f8; padding:30px 0;">
        <tr>
            <td align="center">
                
                <table width="600" cellspacing="0" cellpadding="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:#4CAF50; color:#ffffff; padding:20px; text-align:center;">
                            <h1 style="margin:0; font-size:24px;">Splitwise</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">
                            
                            <h2 style="margin-top:0; color:#333;">
                                Welcome, {{ $user->name }} 👋
                            </h2>

                            <p style="color:#555; font-size:16px; line-height:1.6;">
                                Congratulations! Your account has been successfully registered on <strong>Splitwise API</strong>.
                            </p>

                            <p style="color:#555; font-size:16px; line-height:1.6;">
                                You can now create groups, add expenses, and split costs easily with your friends and family.
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ config('app.url') }}"
                                   style="background:#4CAF50; color:#ffffff; padding:12px 25px; text-decoration:none; border-radius:5px; font-size:16px; display:inline-block;">
                                    Get Started
                                </a>
                            </div>

                            <p style="color:#777; font-size:14px;">
                                If you have any questions, feel free to contact our support team.
                            </p>

                            <p style="color:#333; font-size:15px;">
                                Cheers,<br>
                                <strong>Splitwise Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f0f0f0; padding:15px; text-align:center; font-size:13px; color:#888;">
                            © {{ date('Y') }} Splitwise. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>