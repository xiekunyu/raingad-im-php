<?php
// 中间件配置
return [
    // 别名或分组
    'alias'    => [
        'checkAuth'=>app\common\middleware\CheckAuth::class,
        'manageAuth'=>app\common\middleware\ManageAuth::class,
        'apiAuth'=>app\common\middleware\ApiAuth::class,
        'locale'=>app\common\middleware\Locale::class,
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [],
];
