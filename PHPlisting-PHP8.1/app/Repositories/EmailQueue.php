<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Repositories;

class EmailQueue
{

    public function push($template, $recipient_id, array $data = [], $to = null, $from = null, $replyTo = null)
    {
        if (null !== $template = \App\Models\EmailTemplate::where('name', $template)->first()) {
            if (null === $template->active) {
                return true;
            }

            $email = new \App\Models\Email();
            $email->added_datetime = date('Y-m-d H:i:s');
            $email
                ->setTemplate($template)
                ->setRecipient($recipient_id)
                ->setData($data)
                ->setTo($to)
                ->setFrom($from)
                ->setReplyTo($replyTo);

            return $email->save();
        } else {
            throw new \Exception('Email template is invalid');
        }
    }

    public function dispatch($limit)
    {
        $emails = $this->getScheduledEmails($limit);

        if ($emails->count() > 0) {

            if (config()->email->transport == 'smtp') {
                $transport = (new \Swift_SmtpTransport(config()->email->smtp_host, config()->email->smtp_port, (config()->email->smtp_encryption != '' ? config()->email->smtp_encryption : null)))
                    ->setUsername(d(config()->email->smtp_user))
                    ->setPassword(d(config()->email->smtp_password))
                    ->setTimeout(30);
            } else {
                $transport = (new \Swift_SendmailTransport(config()->email->sendmail_command));
            }

            try {
                $transport->start();
            } catch (\Swift_TransportException $e) {
                return $e->getMessage();
            }

            $mailer = new \Swift_Mailer($transport);

            foreach ($emails as $email) {
                $email->setStatus('failed');

                try {
                    $response = $this->send($email, $mailer, $failed);
                    $email->setStatus('sent');
                } catch (\Swift_TransportException $e) {
                    $email->setError($e->getMessage());
                } catch (\Swift_RfcComplianceException $e) {
                    $email->setError($e->getMessage());
                }

                $email->failed = implode(',', $failed ?? []);
                $email->processed_datetime = date('Y-m-d H:i:s');
                $email->save();

                $transport->reset();
            }

            $transport->stop();
        }

        return null;
    }

    private function getScheduledEmails($limit)
    {
        return \App\Models\Email::query()
            ->where(function ($query) {
                $query
                    ->whereNull('status')
                    ->orWhere('status', 'queued');
            })
            ->where(function ($query) {
                $query
                    ->whereNull('schedule_datetime')
                    ->orWhere($query->raw('NOW() >= schedule_datetime'));
            })
            ->orderBy('added_datetime')
            ->limit($limit)
            ->get();
    }

    private function send(\App\Models\Email $email, \Swift_Mailer $mailer, &$failed)
    {
        $message = (new \Swift_Message())
            ->setFrom($email->getFrom())
            ->setTo($email->getTo())
            ->setReplyTo($email->getReplyTo())
            ->setSubject($email->getSubject())
            ->setBody($email->getBody(), 'text/html');

        if ($email->getPriority() > 0) {
            $message->setPriority($email->getPriority());
        }

        return $mailer->send($message, $failed);
    }

}
