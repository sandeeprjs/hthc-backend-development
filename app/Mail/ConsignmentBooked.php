<?php

namespace App\Mail;

use App\Booking;
use App\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;




class ConsignmentBooked extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $booking,$delivery,$mailto;
    
    public function __construct(Booking $booking,Delivery $delivery, $mailto)
    {
        //
        $this->booking = $booking;
        $this->delivery = $delivery;
        $this->mailto = $mailto;
       //  $this->password = Crypt::decrypt($user->password);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.consignmentBooked');
    }
}
