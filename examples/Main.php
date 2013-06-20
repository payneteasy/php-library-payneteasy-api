<?php
include_once './PaynetProcess.php';

class Main extends PaynetProcess
{
    public function out_form()
    {
        $this->template->content        = '<br /><p class="lead">Examples of use Paynet API.</p>
<p class="lead">Choose one of the examples in the menu.</p>';

        $this->template->out();
    }
}