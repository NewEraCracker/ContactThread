<?php

class Ragtek_ContactThread_ControllerPublic_Misc extends
    #XenForo_ControllerPublic_Misc
   XFCP_Ragtek_ContactThread_ControllerPublic_Misc

{
    /**
     * overwrite the existing method,
     * create a thread instead send the mail
     */
    public function actionContact()
    {
        if ($this->_request->isPost())
        {
            if (!XenForo_Captcha_Abstract::validateDefault($this->_input)) {
                return $this->responseCaptchaFailed();
            }

            $visitor = XenForo_Visitor::getInstance()->toArray();

            if ($visitor['user_id']) {
                $email = $visitor['email'];
            }
            else
            {
                $email = $this->_input->filterSingle('email', XenForo_Input::STRING);

                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    return $this->responseError(new XenForo_Phrase('please_enter_valid_email'));
                }
            }

            $input = $this->_input->filter(array(
                'subject' => XenForo_Input::STRING,
                'message' => XenForo_Input::STRING
            ));

            if (!$visitor['username'] || !$input['subject'] || !$input['message']) {
                return $this->responseError(new XenForo_Phrase('please_complete_required_fields'));
            }

            $this->assertNotFlooding('contact');

            $ip = $this->_request->getClientIp(false);

            $thread_message = new XenForo_Phrase('ragtek_contactthread_message', array(
                'username' => $visitor['username'],
                'email' => $email,
                'ip' => $ip,
                'message' => $input['message']
            ));

            $writer = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread');

            $writer->set('user_id', $visitor['user_id']);
            $writer->set('username', $visitor['username']);

            $writer->set('title', $input['subject']);
            $postWriter = $writer->getFirstMessageDw();
            $postWriter->set('message', $thread_message);
            $nodeId = XenForo_Application::get('options')->ragtekContactThreadForumId;

            $writer->set('node_id', $nodeId);
            if (XenForo_Application::get('options')->ragtek_contactthread_usePrefix) {
                $forumData = $writer->getForumCacheItem($nodeId);

                $writer->set('prefix_id', $forumData['default_prefix_id']);
            }
            $writer->preSave();
            $writer->save();

            if (XenForo_Application::get('options')->ragtekContactThreadSendMail)
            {
                $mailParams = array(
                    'user' => $visitor,
                    'subject' => $input['subject'],
                    'message' => $input['message'],
                    'ip' => $ip
                );

                $mail = XenForo_Mail::create('contact', $mailParams, 0);
                $mail->send(
                    XenForo_Application::get('options')->contactEmailAddress, '', array(),
                    $email, $visitor['username']
                );
            }

            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                $this->getDynamicRedirect(),
                new XenForo_Phrase('your_message_has_been_sent')
            );

        }
        return parent::actionContact();
    }
}