
# TT Jobs

基于 [Swoole](https://www.swoole.com/) 定时管理系统

## UI

![home](home.png)

## 环境要求

- php >= 7.4
- swoole >= 4.7.1

## 配置

1. 修改 `App/Jobs/Conf` 目录下配置

```
App/Jobs/Conf/config.ini    # 指定当前环境
App/Jobs/Conf/dev.php       # 开发环境配置
App/Jobs/Conf/pro.php       # 生产环境配置
```

2. `App/Jobs/Runtime` 目录可写权限

3. 导入 `App/Jobs/Schemas/tt_jobs.sql` 表结构 

## 运行

```
php App/Jobs/bin/server start --d
```

浏览器访问 http://localhost:9501

```
admin: admin    # 管理员
demo: demo      # demo
```

## 注意事项

xxx

## v1.0 TODO

- [ ] 全局常量
- [ ] 一键迁移 crontab

## v2.0 TODO

- [ ] 分布式
