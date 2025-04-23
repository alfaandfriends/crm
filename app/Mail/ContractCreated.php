<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $signUrl;
    public $clientName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($signUrl, $clientName)
    {
        $this->signUrl = $signUrl;
        $this->clientName = $clientName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('ALFA and Friends STEM Programme - Contract for Your Review')
            ->view('emails.contract_created')
            ->with([
                'signUrl' => $this->signUrl,
                'clientName' => $this->clientName,
            ]);
    }
}
