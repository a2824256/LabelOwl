package main

import (
	"github.com/kataras/iris"
	"github.com/kataras/iris/middleware/logger"
	"github.com/kataras/iris/middleware/recover"
	"io/ioutil"
	"log"
)

const (
	upload_path string = "./static/"
)

var (
	imgs []string
)

func Cors(ctx iris.Context) {
	ctx.Header("Access-Control-Allow-Origin", "*")
	if ctx.Request().Method == "OPTIONS" {
		ctx.Header("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,PATCH,OPTIONS")
		ctx.Header("Access-Control-Allow-Headers", "Content-Type, Accept, Authorization")
		ctx.StatusCode(204)
		return
	}
	ctx.Next()
}

func main() {
	//fmt.Println("服务启动")
	app := iris.New()
	app.Logger().SetLevel("debug")
	app.Use(recover.New())
	app.Use(logger.New())
	app.Use(Cors)
	log.Print("服务启动")
	//静态目录路径
	app.StaticWeb("/static", upload_path)
	//返回需要标注的数据
	app.Get("/data", func(ctx iris.Context) {
		files, err := ioutil.ReadDir("./static/images")
		if err != nil {
			log.Fatal(err)
		}
		for _, f := range files {
			imgs = append(imgs, f.Name())
		}
		ctx.JSON(iris.Map{"data": iris.Map{"images": imgs, "labels": iris.Map{"0": "mouth", "1": "consil", "2": "pp-wall"}}, "status": 1})
		imgs = []string{}
	})
	app.Run(iris.Addr(":9090"), iris.WithoutServerError(iris.ErrServerClosed))
}
