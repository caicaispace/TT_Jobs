
## 安装 forever

```
npm install forever -g
```

github

```
https://github.com/foreverjs/forever
```

```
# 作为前台任务启动
$ forever run.js

# 作为服务进程启动
$ forever start run.js

# 停止服务进程
$ forever stop Id

# 重启服务进程
$ forever restart Id

# 监视当前目录的文件变动，一有变动就重启
$ forever -w run.js

# -m 参数指定最多重启次数
$ forever -m 5 run.js

# 列出所有进程
$ forever list

```
