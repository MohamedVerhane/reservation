<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? config('app.name', 'LuxeStay') }}</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #d4a853 0%, #c9963c 100%); padding: 40px 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #1e293b; font-weight: 600; margin-bottom: 20px; }
        .text { font-size: 14px; line-height: 1.7; color: #475569; margin-bottom: 12px; }
        .detail-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-size: 13px; color: #64748b; font-weight: 500; }
        .detail-value { font-size: 13px; color: #1e293b; font-weight: 600; }
        .btn { display: inline-block; background: linear-gradient(135deg, #d4a853, #c9963c); color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; margin-top: 16px; }
        .footer { background: #0f172a; padding: 30px; text-align: center; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 0; }
        .footer a { color: #d4a853; text-decoration: none; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
        .alert { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 14px 18px; margin: 16px 0; }
        .alert-success { background: #dcfce7; border-color: #86efac; }
        .alert-danger { background: #fee2e2; border-color: #fca5a5; }
        .alert-text { font-size: 13px; color: #92400e; margin: 0; }
        .alert-success .alert-text { color: #166534; }
        .alert-danger .alert-text { color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'LuxeStay') }}</h1>
            <p>{{ __('auth.app_name') }}</p>
        </div>

        <div class="content">
            @if(isset($greeting))
                <p class="greeting">{{ $greeting }}</p>
            @endif

            @foreach($lines as $line)
                @if(is_string($line))
                    <p class="text">{!! $line !!}</p>
                @endif
            @endforeach

            @if(isset($actionText) && isset($actionUrl))
                <div style="text-align: center; margin: 24px 0;">
                    <a href="{{ $actionUrl }}" class="btn">{{ $actionText }}</a>
                </div>
            @endif

            @if(isset($actionUrl))
                <p class="text" style="font-size: 12px; color: #94a3b8; word-break: break-all;">
                    {{ __('auth.notif_button_not_working') }} <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
                </p>
            @endif
        </div>

        <div class="footer">
            <p>{{ $footer ?? __('auth.notif_footer') }}</p>
        </div>
    </div>
</body>
</html>
