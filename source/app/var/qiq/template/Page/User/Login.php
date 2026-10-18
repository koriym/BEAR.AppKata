<?php
/** @var Qiq\Engine&Qiq\Helper\Html\HtmlHelpers $this */
$this->setLayout('layout/base');
?>
<h2>UserPage:Login</h2><br>

{{= form() }}
    {{ if (isset($authException)): }}
    <p style="color: red; font-weight: bold;">Authentication error</p>
    {{ endif }}
    {{= $form->input('username') }}
    {{= $form->error('username') }}
    {{= $form->input('password') }}
    {{= $form->error('password') }}
    {{= $form->input('login') }}
</form>
