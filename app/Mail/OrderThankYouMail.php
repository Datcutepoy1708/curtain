<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $subject = ($this->order->payment_status === 'paid')
            ? " Thư Tri Ân & Xác Nhận Đã Thanh Toán Đơn May Rèm #{$this->order->order_code} — CurtainLux"
            : " Thư Tri Ân & Xác Nhận Đặt May Rèm #{$this->order->order_code} — CurtainLux";

        return $this->subject($subject)
            ->view('emails.order-thank-you');
    }
}
