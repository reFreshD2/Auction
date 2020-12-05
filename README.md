Setup project
1. Перейти в деректорию
    `cd /var/www`
2. Склонировать проект
    `git clone https://github.com/reFreshD2/Auction.git`
3. Изменить файл .config.example на ваши настройки
4. Запустить скрипт
    `./setup.sh`
    
Возможные проблемы:

_-bash: ./setup.sh: /bin/bash^M: bad interpreter: No such file or directory_

Выполнять скрипт следующим образом:
1. `sed -i -e 's/\r$//' setup.sh`
2. `./setup.sh`