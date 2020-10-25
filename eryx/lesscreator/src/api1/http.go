package api

import (
	"../../deps/go.net/websocket"
	"../../deps/lessgo/keeper"
	"../../deps/lessgo/passport"
	"../conf"
	"fmt"
	"net/http"
	"time"
)

type Api struct {
	Kpr     keeper.Keeper
	Session passport.Session
	Cfg     conf.Config
}

type ApiResponse struct {
	Status  int    `json:"status"`
	Message string `json:"message"`
}

func (this *Api) Serve(port string) {

	fmt.Println("Api.Serve")
	//kpr = data.NewKprHttp()

	go func() {

		http.HandleFunc("/lesscreator/api/fs-list", this.FsList)
		http.HandleFunc("/lesscreator/api/fs-file-put", this.FsFilePut)
		http.HandleFunc("/lesscreator/api/fs-file-get", this.FsFileGet)
		http.HandleFunc("/lesscreator/api/fs-file-new", this.FsFileNew)
		http.HandleFunc("/lesscreator/api/fs-file-del", this.FsFileDel)
		http.HandleFunc("/lesscreator/api/fs-file-mov", this.FsFileMov)
		http.HandleFunc("/lesscreator/api/fs-file-upl", this.FsFileUpl)
		http.HandleFunc("/lesscreator/api/fs-file-exists", this.FsFileExists)
		http.Handle("/lesscreator/api/fs-save-ws", websocket.Handler(FsSaveWS))

		http.Handle("/lesscreator/api/vs-git-clone-ws", websocket.Handler(this.VsGitClone))

		http.HandleFunc("/lesscreator/api/env-init", this.EnvInit)
		http.HandleFunc("/lesscreator/api/env-pkgsetup", this.EnvPkgSetup)
		http.HandleFunc("/lesscreator/api/env-netport", this.EnvNetPort)

		http.Handle("/lesscreator/api/terminal-ws", websocket.Handler(this.TerminalWS))

		s := &http.Server{
			Addr:    ":" + port,
			Handler: nil,
			//ReadTimeout:    30 * time.Second,
			//WriteTimeout:   30 * time.Second,
			//MaxHeaderBytes: 1 << 20,
		}
		s.ListenAndServe()
	}()

	for {
		time.Sleep(1e9)
	}
}
