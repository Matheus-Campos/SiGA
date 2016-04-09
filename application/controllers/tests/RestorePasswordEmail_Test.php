<?php

/**
 ***** EmailNotification class(on /data_types/notification/EmailNotification) test class.
 *
 *
 * Provide unit tests for the EmailNotification class .
 * To access the report generated by these tests, type on the URL: '../email_notification_test'
 */

require_once("TestCase.php");
require_once(APPPATH."/data_types/notification/emails/RestorePasswordEmail.php");
require_once(APPPATH."/exception/EmailNotificationException.php");

class RestorePasswordEmail_Test extends TestCase{

    public function __construct(){
        parent::__construct($this);
    }
   

    public function getEmailDefaultInformation(){

        $id = 1;
        $userName = "Joao";
        $userEmail = "joao@joao.com";
        $password = bin2hex(openssl_random_pseudo_bytes(4));

        $user = new User($id, $userName, FALSE, $userEmail, FALSE, $password, FALSE);

        $message = "Olá, <b>{$userName}</b>. <br>";
        $message = $message."Esta é uma mensagem automática para a solicitação de nova senha de acesso ao SiGA. <br>";
        $message = $message."Sua nova senha para acesso é: <b>".$password."</b>. <br>";
        $message = $message."Lembramos que para sua segurança ao acessar o sistema com essa senha iremos te redirecionar para a definição de uma nova senha. <br>"; 

        $emailInfo = array();

        $emailInfo['user'] = $user;
        $emailInfo['subject'] = RestorePasswordEmail::RESTORE_PASSWORD_SUBJECT;
        $emailInfo['message'] = $message;       

        return $emailInfo;
    }

    public function shouldReturnEmailInformation(){

        $emailInfo = $this->getEmailDefaultInformation();
        $user = $emailInfo['user'];
        $notes = "";
        try{
            $email = new RestorePasswordEmail($user);
            $notes = "Criou";
        }
        catch (EmailNotificationException $e){
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }
        
        $test_name = "Test if return the receiver name.";
        $this->unit->run($email->getReceiverName(), $user->getName(), $test_name, $notes);

        $test_name = "Test if return the receiver email.";
        $this->unit->run($email->getReceiverEmail(), $user->getEmail(), $test_name, $notes);
       
        $test_name = "Test if return the sender name.";
        $this->unit->run($email->getSenderName(), EmailConstants::SENDER_NAME, $test_name, $notes);

        $test_name = "Test if return the sender email.";
        $this->unit->run($email->getSenderEmail(), EmailConstants::SENDER_EMAIL, $test_name, $notes);

        $test_name = "Test if return the subject.";
        $this->unit->run($email->getSubject(), $emailInfo['subject'], $test_name, $notes);

        $test_name = "Test if return the message.";
        $this->unit->run($email->getMessage(), $emailInfo['message'], $test_name, $notes);
    }

    public function shouldReturnExceptionWithNewPasswordEmpty(){

        $id = 1;
        $userName = "Joao";
        $userEmail = "joao@joao.com";

        $user = new User($id, $userName, FALSE, $userEmail, FALSE, FALSE, FALSE);

        $notes = "";
        try{
            $email = new RestorePasswordEmail($user);
        }
        catch (EmailNotificationException $e){
            $email = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if create an email with empty password.";
        $this->unit->run($email->getMessage(), "" , $test_name, $notes);

    }

}