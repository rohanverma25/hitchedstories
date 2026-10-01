<!DOCTYPE html>
<html lang="en">
<body style="margin:0;padding:24px;background:#f6f1ee;font-family:Arial,Helvetica,sans-serif;color:#2b2b2b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:8px;">
        <tr>
            <td style="padding:28px 32px;">
                <h1 style="margin:0 0 4px;font-size:20px;">New enquiry</h1>
                <p style="margin:0 0 24px;color:#777;font-size:14px;">Received {{ $lead->created_at->format('j F Y, g:i a') }}</p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px;line-height:1.5;">
                    <tr><td style="padding:6px 0;color:#777;width:140px;">Names</td><td>{{ $lead->name }}</td></tr>
                    <tr><td style="padding:6px 0;color:#777;">Email</td><td><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td></tr>
                    <tr><td style="padding:6px 0;color:#777;">Phone</td><td>{{ $lead->phone ?: 'Not given' }}</td></tr>
                    <tr><td style="padding:6px 0;color:#777;">Wedding date</td><td>{{ $lead->event_date?->format('j F Y') ?? 'Not given' }}</td></tr>
                    <tr><td style="padding:6px 0;color:#777;">Venue or city</td><td>{{ $lead->venue ?: 'Not given' }}</td></tr>
                </table>

                <h2 style="margin:24px 0 8px;font-size:15px;">Their plans</h2>
                <p style="margin:0;font-size:15px;line-height:1.6;white-space:pre-line;">{{ $lead->message ?: 'No message.' }}</p>

                <p style="margin:28px 0 0;">
                    <a href="{{ route('admin.leads.show', $lead) }}" style="display:inline-block;padding:10px 20px;background:#b0446a;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;">Open in admin panel</a>
                </p>
                <p style="margin:16px 0 0;color:#999;font-size:12px;">Reply to this email to write to {{ $lead->name }} directly.</p>
            </td>
        </tr>
    </table>
</body>
</html>
