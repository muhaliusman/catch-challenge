<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    private $mailClass;

    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $cc;

    /**
     * @var string
     */
    private $bcc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mailClass, $to, $cc = null, $bcc = null)
    {
        $this->mailClass = $mailClass;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = Mail::to($this->to);

        if ($this->cc) {
            $mail->cc($this->cc);
        }

        if ($this->bcc) {
            $mail->bcc($this->bcc);
        }

        $mail->send($this->mailClass);
    }
}
