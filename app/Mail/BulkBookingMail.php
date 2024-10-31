<?php

namespace App\Mail;

use App\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BulkBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $customerName,$batchId;
    
    public function __construct($batch_id, $sender_name)
    {
        $this->customerName = $sender_name;
        $this->batchId = $batch_id;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bulkBookingMail');
    }
}
