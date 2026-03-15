<?php

namespace App\Mail;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted extends Mailable
{
    use Queueable, SerializesModels;
    public $item;
    /**
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }
    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('【重要】取引が完了しました')
            ->view('emails.transaction_completed');
    }
}
