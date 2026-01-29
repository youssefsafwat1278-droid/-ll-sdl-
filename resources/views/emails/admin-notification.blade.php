<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار جديد</title>
</head>
<body style="margin: 0; padding: 0; background-color: #1a0b2e; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%; border-collapse: separate; background-color: #2d1b4e; border-radius: 16px; overflow: hidden; border: 1px solid #3d2b5e;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 30px; background: linear-gradient(135deg, #37003c 0%, #251042 100%); border-bottom: 2px solid #00ff85;">
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Scout Fantasy</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px; color: #ffffff;">
                            <h2 style="color: #04f5ff; font-size: 20px; font-weight: bold; margin-top: 0; margin-bottom: 20px;">{{ $notification->title }}</h2>
                            
                            <div style="background-color: #1a0b2e; border-radius: 12px; padding: 20px; border: 1px solid #4a3b69;">
                                <p style="color: #e2e8f0; font-size: 16px; line-height: 1.6; margin: 0; white-space: pre-line;">{{ $notification->message }}</p>
                            </div>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center" bgcolor="#00ff85" style="border-radius: 8px;">
                                        <a href="{{ config('app.url') }}" target="_blank" style="font-size: 16px; font-weight: bold; color: #1a0b2e; text-decoration: none; padding: 12px 24px; display: inline-block; border-radius: 8px;">
                                            فتح التطبيق
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #12002b; border-top: 1px solid #3d2b5e;">
                            <p style="color: #6b5b7a; font-size: 12px; margin: 0;">&copy; {{ date('Y') }} Scout Tanzania Fantasy. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
                
            </td>
        </tr>
    </table>
</body>
</html>
