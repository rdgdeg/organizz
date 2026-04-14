<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e4e4e7;">
                <tr>
                    <td style="padding:24px 24px 8px 24px;font-size:18px;font-weight:600;color:#18181b;">
                        {{ $header ?? config('app.name') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 24px 24px 24px;color:#3f3f46;font-size:15px;line-height:1.5;">
                        {{ $slot }}
                    </td>
                </tr>
            </table>
            <p style="font-size:12px;color:#71717a;margin-top:16px;">{{ config('app.name') }}</p>
        </td>
    </tr>
</table>
</body>
</html>
