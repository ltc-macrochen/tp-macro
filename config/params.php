<?php

Yii::setAlias('@app', dirname(__DIR__));
Yii::setAlias('@common', dirname(__DIR__) . '/common');
Yii::setAlias('@runtime', dirname(__DIR__) . '/runtime');

return [
    'adminEmail' => 'admin@example.com',
    'applicationName' => '校花投票大赛',

    // 超级管理员信息配置
    'adminName' => 'root',
    'adminPassword' => 'root',
    'adminNick' => '超级管理员',
];
