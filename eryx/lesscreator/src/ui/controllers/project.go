package controllers

import (
	"../../../deps/lessgo/pagelet"
	"../../../deps/lessgo/utils"
	"../../base"
	"../../conf"
	// "fmt"
)

type Project struct {
	*pagelet.Controller
}

func (c Project) IndexAction() {

	c.ViewData["version"] = conf.Config.Version

	//
	session, err := c.Session.SessionFetch()
	if err != nil || session.Uid == 0 {
		return
	}
}

func (c Project) OpenNavAction() {
	//
}

func (c Project) NewAction() {

	c.ViewData["grpdev"] = base.ProjectGroupByDev
	c.ViewData["grpapp"] = base.ProjectGroupByApp

	c.ViewData["projid"] = utils.StringNewRand36(16)
}

func (c Project) FileNavAction() {

}
