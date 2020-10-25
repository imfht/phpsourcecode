<?php
/**
 * Class MailSenderTask
 *
 * @description('邮件发送处理', '基于队列的邮件处理单元')
 */

use PhaSvc\Base\TaskBase;

class MailSenderTask extends TaskBase
{
    use MultiProcessor;

    public $queueName = 'MAIL_SENDER';

    /**
     * Main
     * @description('邮件发送处理')
     *
     * @param({'type'='int', 'name'='worker_num', 'description'='开启处理任务的Worker数量' })
     */
    public function mainAction(array $params)
    {
        if (isset($params[0]) && is_numeric($params[0]))
            $this->max_precess = $params[0];
        else
            $this->max_precess = swoole_cpu_num();
        $this->CreateMultiProcessor($this->max_precess,
            self::APP_SERVICE_NAME . '.' . strtoupper(__CLASS__),
            TRUE);
    }//end


    /**
     * 查看队列信息
     * @description('查看队列信息')
     */
    public function infoAction()
    {
//        foreach ($this->beanstalk->listTubes() as $tube) {
//            $stats = $this->beanstalk->statsTube($tube);
        $stats = $this->beanstalk->statsTube($this->queueName);
        printf(
            "%s:\n\tready: %d\n\treserved: %d\n",
            $this->queueName,
            $stats['current-jobs-ready'],
            $stats['current-jobs-reserved']
        );
//        }
    }//end


    /**
     * 复写本函数进行真实的任务处理.
     *
     * @DoNotCover
     *
     * @param $index
     * @param $params
     */
    public function RealWork($index, $params)
    {
        try {

            //邮件传送配置
            $mailConfig = include BASE_PATH . '/cli/config/mail.php';
            $mailer     = new \Phalcon\Mailer\Manager($mailConfig);


            static $max_process = 5000;
            static $count = 0;
            $this->beanstalk->useTube($this->queueName);
            while (TRUE) {

                if ($count > $max_process) {
                    $this->dm('[DONE] Worker process finished and exit the processor.',
                        'f1');
                    break;
                }

                if (($job = $this->beanstalk->peekReady()) !== FALSE) {
                    $v = $job->getBody();


                    /******************************** 真实业务处理^ ********************************/

                    $message = $mailer->createMessage();
                    if (isset($v['to'])) {
                        if (is_array($v['to'])) $message->to($v['to'][0], $v['to'][1]);
                        else $message->to($v['to']);
                    }
                    if (isset($v['subject'])) $message->subject($v['subject']);
                    if (isset($v['content'])) $message->content($v['content']);
                    if (isset($v['cc'])) $message->cc($v['cc']); //抄送
                    if (isset($v['bcc'])) $message->bcc($v['cc']); //暗送
                    $message->send();

                    /******************************** 真实业务处理$ ********************************/


                    $job->delete();//完成任务, 删除

                    $count++;

                    usleep(20000); //调试延时使用,生产环境请删除
                } else {
                    $this->dm('[QUEUE_EMPTY] SLEEPING...', 'f1');
                    sleep(3);
                }

            }
        } catch (\Exception $e) {
            $this->logger->error(
                __FILE__ . '|' . __CLASS__ . '|' . __FUNCTION__ . '|'
                . $e->getMessage() . '|' . $e->getTraceAsString());
            $this->dm($e->getMessage(), 'f1');
        }

    }//end

}//end
