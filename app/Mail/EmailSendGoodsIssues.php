<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailSendGoodsIssues extends Mailable
{
    use Queueable, SerializesModels;

	public $title, $content,$data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title,$data)
    {
        //
        $this->title = $title;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		//return $this->view('view.name');
       return $this->from(env("MAIL_FROM_ADDRESS"))
                   ->subject($this->title)
                   ->view('email_goods_issues')
                   ->with($this->data);
    }
}
