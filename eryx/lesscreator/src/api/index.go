package api

import (
	"../../deps/lessgo/pagelet"
	"../../deps/lessgo/utils"
	"../base"
	"io"
)

type Index struct {
	*pagelet.Controller
}

func (c Index) IndexAction() {

	c.AutoRender = false

	var rsp struct {
		base.ServiceResponse
	}
	rsp.Status = 400
	rsp.Message = "Bad Request"

	defer func() {
		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(c.Response.Out, rspj)
		}
	}()

}
