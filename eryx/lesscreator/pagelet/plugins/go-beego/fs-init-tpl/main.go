package main

import (
	"./controllers"
	"github.com/astaxie/beego"
)

func main() {
	beego.Router("/", &controllers.MainController{})
	beego.Run()
}

