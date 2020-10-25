#!/bin/bash
while [ true ]; do
#/bin/sleep 1
/usr/bin/curl http://thinkask.com/wl/cron/linux_creatmoidata >> /mnt/hgfs/selfproject/thinkask/cron/log_creatmoidata.log
/usr/bin/curl http://thinkask.com/wl/check/checkorder >> /mnt/hgfs/selfproject/thinkask/cron/log_checkorder.log
done
