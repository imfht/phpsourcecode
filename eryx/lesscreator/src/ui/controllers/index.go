package controllers

import (
	"../../../deps/lessgo/pagelet"
	"../../../deps/lessgo/service/lessids"
	"../../conf"
	"fmt"
	"net/http"
)

type Index struct {
	*pagelet.Controller
}

func (c Index) IndexAction() {

	c.ViewData["version"] = conf.Config.Version

	//
	session, err := c.Session.SessionFetch()
	if err != nil || session.Uid == 0 {
		c.RenderRedirect(lessids.LoginUrl(c.Request.RawAbsUrl()))
		return
	}

	ck := &http.Cookie{
		Name:    "access_userkey",
		Value:   session.Uuid,
		Path:    "/",
		Expires: session.Expired.UTC(),
	}
	http.SetCookie(c.Response.Out, ck)

	//
	if c.Params.Get("access_token") != "" {

		ck := &http.Cookie{
			Name:  "access_token",
			Value: session.AccessToken,
			Path:  "/",
			//HttpOnly: true,
			Expires: session.Expired.UTC(),
		}
		http.SetCookie(c.Response.Out, ck)

		c.RenderRedirect("/lesscreator")
		return
	}

	//
	c.ViewData["lessfly_api"] = conf.Config.LessFlyApi
}

func (c Index) WsAction() {

	for {

		fmt.Println("WsAction for")

		var msg string

		if err := c.Request.WebSocket.Receive(&msg); err != nil {
			return
		}

		// if err := websocket.Message.Receive(c.Request.Websocket, &msg); err != nil {
		// 	c.Request.Websocket.Close()
		// 	return
		// }
		//fmt.Println("FsSaveWS: ", msg)

		// var req struct {
		// 	MsgReply string `json:"msgreply"`
		// 	Data     struct {
		// 		Urid     string `json:"urid"`
		// 		Path     string `json:"path"`
		// 		Body     string `json:"body"`
		// 		SumCheck string `json:"sumcheck"`
		// 	} `json:"data"`
		// }
		// err = utils.JsonDecode(msg, &req)
		// if err != nil {
		// 	return
		// }

		// fp, err := os.OpenFile(req.Data.Path, os.O_RDWR|os.O_CREATE, 0754)
		// if err != nil {
		// 	return
		// } else {

		// 	fp.Seek(0, 0)
		// 	fp.Truncate(int64(len(req.Data.Body)))

		// 	if _, err = fp.WriteString(req.Data.Body); err != nil {
		// 		fmt.Println(err)
		// 	}
		// }
		// fp.Close()

		var ret struct {
			Status   int    `json:"status"`
			MsgReply string `json:"msgreply"`
		}
		ret.Status = 200
		ret.MsgReply = "OKKKKKKKKKKKK"

		fmt.Println("WsAction back")

		if err := c.Request.WebSocket.JsonSend(ret); err != nil {
			return
		}

		// if err = websocket.JSON.Send(c.Request.Websocket, ret); err != nil {
		// 	c.Request.Websocket.Close()
		// 	return
		// }
	}
}

func (c Index) BoxListAction() {

}

func (c Index) DeskAction() {

	c.ViewData["lc_version"] = conf.Config.Version

	//
	session, err := c.Session.SessionFetch()
	if err != nil || session.Uid == 0 {
		return
	}

	c.ViewData["nav_user"] = map[string]string{
		"lessids_url":         lessids.ServiceUrl,
		"lessids_url_signout": lessids.ServiceUrl + "/service/sign-out?access_token=" + session.AccessToken,
		"access_token":        session.AccessToken,
		"name":                session.Name,
		"ukey":                session.Uuid,
		"photo":               lessids.ServiceUrl + "/service/photo/" + session.Uuid,
	}
}
