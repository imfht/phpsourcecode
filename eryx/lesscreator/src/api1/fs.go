package api

import (
	"../../deps/go.net/websocket"
	"../utils"
	"encoding/base64"
	"errors"
	"fmt"
	"io"
	"io/ioutil"
	"mime"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"regexp"
	"strings"
)

type FsFile struct {
	Path     string `json:"path"`
	Name     string `json:"name"`
	Size     int64  `json:"size"`
	Mime     string `json:"mime"`
	Body     string `json:"body"`
	SumCheck string `json:"sumcheck"`
	IsDir    bool   `json:"isdir"`
	ModTime  string `json:"modtime"`
	//Mode     uint32    `json:"mode"`
	//Error    string    `json:"error"`
}

type FsSaveWSRsp struct {
	Status   int    `json:"status"`
	Message  string `json:"message"`
	MsgReply string `json:"msgreply"`
	Data     struct {
		Urid     string `json:"urid"`
		SumCheck string `json:"sumcheck"`
	} `json:"data"`
}

func FsSaveWS(ws *websocket.Conn) {

	var err error

	for {
		var msg string
		if err := websocket.Message.Receive(ws, &msg); err != nil {
			ws.Close()
			return
		}
		//fmt.Println("FsSaveWS: ", msg)

		var req struct {
			MsgReply string `json:"msgreply"`
			Data     struct {
				Urid     string `json:"urid"`
				Path     string `json:"path"`
				Body     string `json:"body"`
				SumCheck string `json:"sumcheck"`
			} `json:"data"`
		}
		err = utils.JsonDecode(msg, &req)
		if err != nil {
			return
		}

		fp, err := os.OpenFile(req.Data.Path, os.O_RDWR|os.O_CREATE, 0754)
		if err != nil {
			return
		} else {

			fp.Seek(0, 0)
			fp.Truncate(int64(len(req.Data.Body)))

			if _, err = fp.WriteString(req.Data.Body); err != nil {
				fmt.Println(err)
			}
		}
		fp.Close()

		var ret FsSaveWSRsp
		ret.Status = 200
		ret.MsgReply = req.MsgReply
		ret.Data.Urid = req.Data.Urid
		ret.Data.SumCheck = req.Data.SumCheck
		if err = websocket.JSON.Send(ws, ret); err != nil {
			ws.Close()
			return
		}
	}
}

func (this *Api) FsList(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
		Data []FsFile `json:"data"`
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        struct {
			Path   string `json:"path"`
			Subdir bool   `json:"subdir"`
		} `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	reg, _ := regexp.Compile("/+")
	req.Data.Path = "/" + strings.Trim(reg.ReplaceAllString(req.Data.Path, "/"), "/")

	rsp.Data = dirlist(req.Data.Path, "", req.Data.Subdir)

	rsp.Status = 200
}

func dirlist(path, ppath string, subdir bool) []FsFile {

	var ret []FsFile

	globpath := path
	if !strings.Contains(globpath, "*") {
		globpath += "/*"
	}

	rs, err := filepath.Glob(globpath)

	if err != nil {
		return ret
	}

	if len(ppath) > 0 {
		ppath += "/"
	}

	for _, v := range rs {

		var file FsFile
		file.Path = v

		st, err := os.Stat(v)
		if os.IsNotExist(err) {
			continue
		}

		file.Name = ppath + st.Name()
		file.Size = st.Size()
		file.IsDir = st.IsDir()
		file.ModTime = st.ModTime().Format("2006-01-02T15:04:05Z07:00")

		if !st.IsDir() {
			file.Mime = fsFileMime(v)
		} else if subdir {
			subret := dirlist(path+"/"+st.Name(), ppath+st.Name(), subdir)
			for _, v := range subret {
				ret = append(ret, v)
			}
		}

		ret = append(ret, file)
	}

	return ret
}

func fsFileMime(v string) string {

	// TODO
	//  ... add more extension types
	ctype := mime.TypeByExtension(filepath.Ext(v))

	if ctype == "" {
		fp, err := os.Open(v)
		if err == nil {

			defer fp.Close()

			if ctn, err := ioutil.ReadAll(fp); err == nil {
				ctype = http.DetectContentType(ctn)
			}
		}
	}

	ctypes := strings.Split(ctype, ";")
	if len(ctypes) > 0 {
		ctype = ctypes[0]
	}

	return ctype
}

func (this *Api) FsFileGet(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
		Data FsFile `json:"data"`
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        FsFile `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	file, status, err := fsFileGetRead(req.Data.Path)
	if err != nil {
		rsp.Status = status
		rsp.Message = err.Error()
		return
	}

	rsp.Data = file

	rsp.Status = status
}

func (this *Api) FsFileExists(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
		Data FsFile `json:"data"`
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        FsFile `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	reg, _ := regexp.Compile("/+")
	path := "/" + strings.Trim(reg.ReplaceAllString(req.Data.Path, "/"), "/")

	_, err = os.Stat(path)
	if err != nil || os.IsNotExist(err) {
		rsp.Status = 404
		rsp.Message = "File Not Found"
	} else {
		rsp.Status = 200
		rsp.Message = ""
	}
}

func fsFileGetRead(path string) (FsFile, int, error) {

	var file FsFile
	file.Path = path

	reg, _ := regexp.Compile("/+")
	path = "/" + strings.Trim(reg.ReplaceAllString(path, "/"), "/")

	st, err := os.Stat(path)
	if err != nil || os.IsNotExist(err) {
		return file, 404, errors.New("File Not Found")
	}
	file.Size = st.Size()

	if st.Size() > (2 * 1024 * 1024) {
		return file, 413, errors.New("File size is too large") // Request Entity Too Large
	}

	fp, err := os.OpenFile(path, os.O_RDWR, 0754)
	if err != nil {
		return file, 500, errors.New("File Can Not Open")
	}
	defer fp.Close()

	ctn, err := ioutil.ReadAll(fp)
	if err != nil {
		return file, 500, errors.New("File Can Not Readable")
	}
	file.Body = string(ctn)

	// TODO
	ctype := mime.TypeByExtension(filepath.Ext(path))
	if ctype == "" {
		ctype = http.DetectContentType(ctn)
	}
	ctypes := strings.Split(ctype, ";")
	if len(ctypes) > 0 {
		ctype = ctypes[0]
	}
	file.Mime = ctype

	return file, 200, nil
}

func (this *Api) FsFilePut(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        FsFile `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}
	//fmt.Println("AAA")

	sess := this.Session.Instance(req.AccessToken)
	if sess.Uid == "0" || sess.Uid == "" {
		rsp.Status = 401
		rsp.Message = "Unauthorized"
		return
	}
	//fmt.Println(sess)
	osuser := "lc" + sess.Uname

	if err := fsFilePutWrite(req.Data, osuser); err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	rsp.Status = 200
	rsp.Message = ""
}

func fsFilePutWrite(file FsFile, osuser string) error {

	reg, _ := regexp.Compile("/+")
	path := "/" + strings.Trim(reg.ReplaceAllString(file.Path, "/"), "/")

	dir := filepath.Dir(path)
	if st, err := os.Stat(dir); os.IsNotExist(err) {

		if err = os.MkdirAll(dir, 0755); err != nil {
			return err
		}

		if err := os.Chmod(dir, 0755); err != nil {
			//return err
		}

	} else if !st.IsDir() {
		return errors.New("Can not create directory, File exists")
	}

	fp, err := os.OpenFile(path, os.O_RDWR|os.O_CREATE, 0755)
	if err != nil {
		return err
	}
	defer fp.Close()

	fp.Seek(0, 0)
	fp.Truncate(int64(len(file.Body)))
	if _, err = fp.Write([]byte(file.Body)); err != nil {
		return err
	}

	//if err := os.Chmod(path, 0755); err != nil {
	//return err
	//}

	if _, err := exec.Command("/bin/chmod", "-R", "+rx", dir).Output(); err != nil {

	}

	if _, err := exec.Command("/bin/chown", "-R", osuser+":"+osuser, dir).Output(); err != nil {
		//
	}

	return nil
}

func (this *Api) FsFileNew(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        struct {
			Type string `json:"type"`
			Path string `json:"path"`
		} `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	sess := this.Session.Instance(req.AccessToken)
	if sess.Uid == "0" || sess.Uid == "" {
		rsp.Status = 401
		rsp.Message = "Unauthorized"
		return
	}
	osuser := "lc" + sess.Uname

	reg, _ := regexp.Compile("/+")
	path := strings.Trim(reg.ReplaceAllString(req.Data.Path, "/"), "/")

	var pd string
	if req.Data.Type == "file" {
		ps := strings.Split(path, "/")
		pd = "/" + strings.Join(ps[0:len(ps)-1], "/")
	} else if req.Data.Type == "dir" {
		pd = "/" + path
	} else {
		rsp.Status = 500
		rsp.Message = "Type is incorrect"
		return
	}

	if _, err := os.Stat(pd); os.IsNotExist(err) {

		if err = os.MkdirAll(pd, 0755); err != nil {
			rsp.Message = "Can Not Create Folder /" + pd
			rsp.Status = 500
			return
		}
	}

	if req.Data.Type == "dir" {
		rsp.Status = 200

		if _, err := exec.Command("/bin/chown", "-R", osuser+":"+osuser, pd).Output(); err != nil {
			//
		}
		return
	}

	fp, err := os.OpenFile("/"+path, os.O_RDWR|os.O_CREATE, 0754)
	if err != nil {
		//rsp.Message = "Can Not Open /" + path
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}
	defer fp.Close()

	if _, err = fp.Write([]byte("\n\n")); err != nil {
		//rsp.Message = "File is not Writable"
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	if _, err := exec.Command("/bin/chown", "-R", osuser+":"+osuser, pd).Output(); err != nil {
		//
	}

	rsp.Status = 200
}

func (this *Api) FsFileDel(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        string `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	reg, _ := regexp.Compile("/+")
	path := strings.Trim(reg.ReplaceAllString(req.Data, "/"), "/")

	if err := os.Remove("/" + path); err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	rsp.Status = 200
	rsp.Message = "OK"
}

func (this *Api) FsFileMov(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        struct {
			PathNew string `json:"pathnew"`
			PathPre string `json:"pathpre"`
		} `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	reg, _ := regexp.Compile("/+")
	pathpre := "/" + strings.Trim(reg.ReplaceAllString(req.Data.PathPre, "/"), "/")

	pathnew := "/" + strings.Trim(reg.ReplaceAllString(req.Data.PathNew, "/"), "/")

	if pathnew == pathpre {
		rsp.Status = 200
		rsp.Message = ""
		return
	}

	dir := filepath.Dir(pathnew)
	if _, err := os.Stat(dir); os.IsNotExist(err) {

		if err = os.MkdirAll(dir, 0750); err != nil {
			rsp.Status = 500
			rsp.Message = err.Error()
			return
		}
	}

	if err := os.Rename(pathpre, pathnew); err != nil {
		rsp.Status = 500
		rsp.Message = "102:" + err.Error()
		return
	}
	/*cp, err := exec.LookPath("cp")
	  if err != nil {
	      rsp.Status = 500
	      rsp.Message = err.Error()
	      return
	  }

	  fmt.Println(cp, "-rp", pathpre, pathnew)

	  if _, err := exec.Command(cp, "-rp", pathpre, pathnew).Output(); err != nil {
	      rsp.Status = 500
	      rsp.Message = err.Error()
	      return
	  }

	  if err := os.Remove(pathpre); err != nil {
	      rsp.Status = 500
	      rsp.Message = err.Error()
	      return
	  }
	*/

	rsp.Status = 200
	rsp.Message = ""
	return
}

func (this *Api) FsFileUpl(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
	}

	defer func() {

		if rsp.Status == 0 {
			rsp.Status = 500
			rsp.Message = "Internal Server Error"
		}

		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        struct {
			FsFile
			ProjId string `json:"projid"`
		} `json:"data"`
	}
	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	sess := this.Session.Instance(req.AccessToken)
	if sess.Uid == "0" || sess.Uid == "" {
		rsp.Status = 401
		rsp.Message = "Unauthorized"
		return
	}
	osuser := "lc" + sess.Uname

	dataurl := strings.SplitAfter(req.Data.Body, ";base64,")
	if len(dataurl) != 2 {
		rsp.Status = 500
		rsp.Message = "Bad Request"
		return
	}

	data, err := base64.StdEncoding.DecodeString(dataurl[1])
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	reg, _ := regexp.Compile("/+")
	path := "/" + strings.Trim(reg.ReplaceAllString(req.Data.Path, "/"), "/")

	ps := strings.Split(path, "/")
	pd := "/" + strings.Join(ps[0:len(ps)-1], "/")

	if _, err := os.Stat(pd); os.IsNotExist(err) {

		if err = os.MkdirAll(pd, 0755); err != nil {
			rsp.Message = "Can Not Create Folder /" + pd
			rsp.Status = 500
			return
		}
	}

	fp, err := os.OpenFile(path, os.O_RDWR|os.O_CREATE, 0755)
	if err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}
	defer fp.Close()

	fp.Seek(0, 0)
	fp.Truncate(int64(len(data)))
	if _, err = fp.Write(data); err != nil {
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	dir := this.Cfg.LessFlyDir + "/spot/" + sess.Uname + "/app/" + req.Data.ProjId
	//fmt.Println(dir)
	if _, err := exec.Command("/bin/chmod", "-R", "+rx", dir).Output(); err != nil {

	}

	if _, err := exec.Command("/bin/chown", "-R", osuser+":"+osuser, dir).Output(); err != nil {
		//
	}

	rsp.Status = 200
}
