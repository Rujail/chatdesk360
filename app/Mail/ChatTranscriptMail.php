<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Carbon\Carbon;

class ChatTranscriptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $siteId;
    public $visitorId;
    public $domainId;
    public $messages = [];
    public $siteName = 'ChatDesk360';

    public function __construct($siteId, $visitorId, $domainId)
    {
        $this->siteId    = $siteId;
        $this->visitorId = $visitorId;
        $this->domainId  = $domainId;
    }

    public function build()
    {
        $this->fetchMessages();

        // ★ Use markdown() to keep the default header/footer
        return $this->subject('Your Chat Transcript - ' . $this->siteName)
                    ->markdown('emails.chat-transcript')
                    ->with([
                        'messages' => $this->messages,
                        'siteName' => $this->siteName,
                    ]);
    }

    protected function fetchMessages()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/firebase-auth.json'))
                ->withDatabaseUri(config('services.firebase.db_url'));

            $database = $factory->createDatabase();
            $reference = "chats/{$this->domainId}/general/{$this->visitorId}/messages";
            
            $snapshot = $database->getReference($reference)
                ->orderByChild('timestamp')
                ->getSnapshot();

            $rawMessages = $snapshot->getValue();
            
            if (!$rawMessages || !is_array($rawMessages)) {
                $this->messages = [];
                return;
            }

            foreach ($rawMessages as $msg) {
                $sender = 'System';
                $message = $msg['message'] ?? '';
                $type = 'system';
                
                // ★★★ EXTRACT FILE DATA ★★★
                $fileUrl = $msg['fileUrl'] ?? null;
                $fileType = $msg['fileType'] ?? null;
                $fileName = $msg['fileName'] ?? null;

                if (($msg['sender'] ?? '') === 'agent') {
                    $sender = $msg['agent_name'] ?? 'Support Agent';
                    $type = 'agent';
                } elseif (($msg['sender'] ?? '') === 'visitor') {
                    $sender = $msg['username'] ?? 'Visitor';
                    $type = 'visitor';
                } elseif (isset($msg['type']) && $msg['type'] === 'info_filled') {
                    $sender = 'System';
                    $name = $msg['visitor_name'] ?? '';
                    $email = $msg['visitor_email'] ?? '';
                    $message = "Info shared: {$name}" . ($email ? " ({$email})" : '');
                    $type = 'system';
                } elseif (($msg['sender'] ?? '') === 'system') {
                    $sender = 'System';
                    $type = 'system';
                }

                if (isset($msg['type']) && $msg['type'] === 'post_chat_response') {
                    $sender = 'System';
                    $type = 'system';
                    $responseData = $msg['response_data'] ?? [];
                    $parts = [];
                    foreach ($responseData as $question => $answer) {
                        $displayAnswer = is_array($answer) ? implode(', ', $answer) : $answer;
                        $parts[] = "{$question}: {$displayAnswer}";
                    }
                    $message = '📋 Post-chat Feedback — ' . implode(' | ', $parts);
                }

                $time = isset($msg['timestamp']) 
                    ? Carbon::createFromTimestamp(floor($msg['timestamp'] / 1000))->format('M d, Y h:i A') 
                    : '';

                // ★★★ MUST INCLUDE fileType, fileUrl, fileName ★★★
                $this->messages[] = [
                    'sender'   => $sender,
                    'message'  => $message,
                    'time'     => $time,
                    'type'     => $type,
                    'fileType' => $fileType,
                    'fileUrl'  => $fileUrl,
                    'fileName' => $fileName,
                ];
            }

        } catch (\Exception $e) {
            \Log::error('[Transcript] Failed to fetch from Firebase: ' . $e->getMessage());
            $this->messages = [];
        }
    }
}