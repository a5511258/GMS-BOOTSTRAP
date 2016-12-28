#!/bin/sh
sudo mkdir Export
sudo chown -R www-data ../CourtGms
sudo chgrp -R www-data ../CourtGms
#将所有文件权限改为rw-r--r--
sudo chmod -R 644 ../CourtGms
#将所有目录改为可读可执行(这样才能创建文件)
sudo chmod 755 `sudo find ../CourtGms -type d`

