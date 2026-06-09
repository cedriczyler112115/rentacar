## Email Notifications (Bookings)

### What gets sent
- Owner booking notification: triggered after a renter submits a booking via `/book/{enc_id}` and the rental is saved.
- Owner cancellation notification: triggered when the renter cancels from `/my-bookings`.

Emails are queued (job-based) and stored in `booking_email_logs` for audit.

### Required configuration
Set your mailer in `.env` (example: SMTP):

```
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="Auto Amegos Rent-a-Car"
```

### Best-practice domain authentication (outside code)
Configure these DNS records for the sending domain:
- SPF
- DKIM
- DMARC

If using a provider like SES/Mailgun/SendGrid, follow their domain verification steps and use their recommended DNS records.

### Queue worker
To send emails asynchronously, run a queue worker:

```
php artisan queue:work
```

### Troubleshooting
- Check `booking_email_logs` for status `failed` and the `error_message`.
- Check application logs for `booking_email_send_failed`.
- If emails never send, confirm the queue worker is running and the mailer is configured.

