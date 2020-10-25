<?php
/**
 * Command test
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2013
 *
 */
class TestCommand extends CConsoleCommand
{

    public function init()
    {
        set_time_limit(0);
        echo "init. \t\n";
    }

    /**
     * /usr/local/php/bin/php /linux_path/protected/yiic test do_test
     */
    public function actionDo_test()
    {
        echo "in. \t\n";

        echo "out. \t\n";
    }
}
?>
