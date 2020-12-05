#!/bin/bash

cp -- ../.config.example /etc/nginx/sites-available/config
ln /etc/nginx/sites-available/config /etc/nginx/sites-enabled/config

#Ваш порт из .config.example
ufw allow 6060
sudo service nginx reload