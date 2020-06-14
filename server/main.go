package main

import (
	"fmt"
	"github.com/kataras/iris"
)

const (
	upload_path string = "./static/"
)

func main()  {
	fmt.Println("服务启动")
	app := iris.New()
	//静态目录路径
	app.StaticWeb("/static",upload_path)
	app.Run(iris.Addr(":9090"))
}