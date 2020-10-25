package api

import (
	"../../deps/go.net/websocket"
	"../../deps/pty"
	"../utils"
	"fmt"
	"os/exec"
	"time"
)

type GitCloneResponse struct {
	ApiResponse
	Data struct {
		Output string `json:"output"`
	} `json:"data"`
}

const CMD_IOBUF_LEN = 512

func (this *Api) VsGitClone(ws *websocket.Conn) {

	var err error
	var rsp GitCloneResponse

	ws.SetDeadline(time.Now().Add(180 * time.Second))

	defer func() {

		fmt.Println("ws all return")
		if err := websocket.JSON.Send(ws, rsp); err != nil {
			//
		}

		ws.Close()
	}()

	var msg string
	if err := websocket.Message.Receive(ws, &msg); err != nil {
		return
	}

	//fmt.Println("msg", msg)
	var req struct {
		AccessToken string `json:"access_token"`
		Data        struct {
			GitUrl    string `json:"git_url"`
			GitTarget string `json:"git_target"`
			GitBase   string `json:"git_base"`
		} `json:"data"`
	}
	err = utils.JsonDecode(msg, &req)
	if err != nil {
		return
	}

	sess := this.Session.Instance(req.AccessToken)
	if sess.Uid == "0" || sess.Uid == "" {
		rsp.Status = 401
		rsp.Message = "Unauthorized"
		return
	}

	cmd := exec.Command("/bin/su", "lceryx")
	fp, err := pty.Start(cmd)
	if err != nil {
		return
	}
	defer func() {
		fmt.Println("fp exit/close")
		fp.Write([]byte{4}) // EOT
		fp.Close()
	}()

	fp.Write([]byte("git clone " + req.Data.GitUrl + " " +
		req.Data.GitBase + "/" + req.Data.GitTarget + "\n"))

	go func() {

		for {
			var buf [CMD_IOBUF_LEN]byte
			n, err := fp.Read(buf[0:])
			if err != nil {
				fmt.Println("#004", err)
				return
			}

			rsp.Status = 200
			rsp.Data.Output = string(buf[0:n])

			if err = websocket.JSON.Send(ws, rsp); err != nil {
				fmt.Println("#003", err)
				return
			}

			fmt.Print(string(buf[0:n]))
		}

		fmt.Println("ws read close")
	}()

	for {

		//fmt.Println("ws wait Receive")
		var msg string
		if err := websocket.Message.Receive(ws, &msg); err != nil {
			fmt.Println("#001", err)
			//if err == io.EOF {

			//}
			return
		}

		//fmt.Println("ws Receive:", msg)

		var req struct {
			AccessToken string `json:"access_token"`
			Data        struct {
				Input string `json:"input"`
			} `json:"data"`
		}
		err = utils.JsonDecode(msg, &req)
		if err != nil {
			fmt.Println("#002", err)
			return
		}

		// TODO auth

		// New Command INPUT
		fp.Write([]byte(req.Data.Input))
	}

	fmt.Println("ws receive close")
}
