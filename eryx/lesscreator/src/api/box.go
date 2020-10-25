package api

import (
	"../../deps/lessgo/pagelet"
	"../../deps/lessgo/utils"
	"../base"
	"io"
)

type Box struct {
	*pagelet.Controller
}

func (c Box) BoxAction() {

	c.AutoRender = false

	var rsp struct {
		base.ServiceResponse
		Data struct {
			User    string `json:"user"`
			BaseDir string `json:"basedir"`
		} `json:"data"`
	}
	rsp.Status = 400
	rsp.Message = "Bad Request"

	defer func() {
		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(c.Response.Out, rspj)
		}
	}()

}
