package api

import (
	lutils "../../deps/lessgo/utils"
	"../utils"
	"fmt"
	"io"
	"io/ioutil"
	"net/http"
	"os"
	"os/exec"
	"os/user"
	"strconv"
)

type ApiEnvResponse struct {
	ApiResponse
	Data struct {
		User    string `json:"user"`
		BaseDir string `json:"basedir"`
	} `json:"data"`
}

func (this *Api) EnvPkgSetup(w http.ResponseWriter, r *http.Request) {

	var rsp ApiEnvResponse
	rsp.Status = 400
	rsp.Message = "Bad Request"

	defer func() {
		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		fmt.Println("ere", rsp)
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
		Data        struct {
			ProjId string `json:"projid"`
		} `json:"data"`
	}

	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		return
	}

	sess := this.Session.Instance(req.AccessToken)

	if sess.Uid == "0" || sess.Uid == "" {
		rsp.Status = 401
		rsp.Message = "Unauthorized"
		return
	}
	//fmt.Println(sess)

	// User ID
	osuser := "lc" + sess.Uname

	rs := this.Kpr.LocalNodeGet("/lf/pkg/" + req.Data.ProjId)
	if rs.Body == "" {
		rsp.Status = 404
		rsp.Message = "Package Not Found"
		return
	}

	var pkg struct {
		Version string `json:"version"`
		Release string `json:"release"`
		Dist    string `json:"dist"`
		Arch    string `json:"arch"`
	}
	err = utils.JsonDecode(rs.Body, &pkg)
	if err != nil {
		return
	}

	pkgpath := fmt.Sprintf("%s/var/pkg/%s-%s-%s.%s.%s",
		this.Cfg.LessFlyDir, req.Data.ProjId, pkg.Version, pkg.Release, pkg.Dist, pkg.Arch)
	if _, err := os.Stat(pkgpath); os.IsNotExist(err) {
		rsp.Status = 404
		rsp.Message = "Package Not Found"
		return
	}
	userdir := this.Cfg.LessFlyDir + "/spot/" + sess.Uname
	instdir := userdir + "/app/" + req.Data.ProjId

	cmdrsync, err := exec.LookPath("rsync")
	if err != nil {
		return
	}

	cmdchown, err := exec.LookPath("chown")
	if err != nil {
		return
	}

	makedir(instdir, 0, 0, 0755)
	if _, err := exec.Command(cmdrsync, "-avz", "--delete", pkgpath+"/", instdir).Output(); err != nil {
		fmt.Println("rsync error", err)
		rsp.Status = 500
		rsp.Message = err.Error()
		return
	}

	if _, err := exec.Command(cmdchown, "-R", osuser+":"+osuser, instdir).Output(); err != nil {
		//
	}

	rsp.Status = 200
	rsp.Message = ""
}

func (this *Api) EnvNetPort(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
		Data struct {
			Port string `json:"port"`
		} `json:"data"`
	}

	defer func() {
		//fmt.Println(rsp)
		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	err, _, port := lutils.NetFreePort(30000, 65000)
	if err != nil {
		rsp.Status = 500
		return
	}

	rsp.Status = 200
	rsp.Data.Port = port
}

func (this *Api) EnvInit(w http.ResponseWriter, r *http.Request) {

	var rsp struct {
		ApiResponse
		Data struct {
			User    string `json:"user"`
			BaseDir string `json:"basedir"`
		} `json:"data"`
	}
	rsp.Status = 400
	rsp.Message = "Bad Request"

	defer func() {
		if rspj, err := utils.JsonEncode(rsp); err == nil {
			io.WriteString(w, rspj)
		}
		r.Body.Close()
	}()

	body, err := ioutil.ReadAll(r.Body)
	if err != nil {
		return
	}

	var req struct {
		AccessToken string `json:"access_token"`
	}

	err = utils.JsonDecode(string(body), &req)
	if err != nil {
		return
	}

	sess := this.Session.Instance(req.AccessToken)

	if sess.Uid == "0" || sess.Uid == "" {
		rsp.Status = 401
		rsp.Message = "Unauthorized"
		return
	}

	userpath := this.Cfg.LessFlyDir + "/spot/" + sess.Uname

	// User ID
	osuser := "lc" + sess.Uname
	u, e := user.Lookup(osuser)
	if e != nil {
		defshell, err := exec.LookPath("bash")
		if err != nil {
			return
		}
		if _, err := exec.Command("/usr/sbin/useradd", "-d", userpath,
			"-s", defshell, osuser).Output(); err != nil {
			return
		}
		u, e = user.Lookup(osuser)
	}
	uuid, _ := strconv.Atoi(u.Uid)
	ugid, _ := strconv.Atoi(u.Gid)
	//fmt.Println(userpath)
	// Instance Folder
	makedir(userpath, uuid, ugid, 0750)
	makedir(userpath+"/webpub", uuid, ugid, 0755)
	makedir(userpath+"/conf", uuid, ugid, 0777)
	makedir(userpath+"/data", uuid, ugid, 0777)
	e = makedir(userpath+"/app", uuid, ugid, 0777)
	if e != nil {
		fmt.Println("EE", e)
	}

	//
	if _, err := exec.Command("/bin/cp", "-rp",
		this.Cfg.LessFlyDir+"/misc/php/user.index.php",
		userpath+"/webpub/index.php").Output(); err != nil {

		//return
	}

	if _, err := exec.Command("/bin/cp", "-rp",
		this.Cfg.Prefix+"/misc/bash/bashrc", userpath+"/.bashrc").Output(); err != nil {
		// TODO
	}
	if _, err := exec.Command(this.Cfg.LessFlyDir+"/bin/lessfly-env-filter",
		"--lessfly_dir="+this.Cfg.LessFlyDir,
		"--user="+sess.Uname,
		"--file="+userpath+"/.bashrc").Output(); err != nil {
		// TODO
	}

	if _, err := exec.Command("/bin/cp", "-rp",
		this.Cfg.Prefix+"/misc/nodejs/npmrc", userpath+"/.npmrc").Output(); err != nil {
		// TODO
	}
	if _, err := exec.Command(this.Cfg.LessFlyDir+"/bin/lessfly-env-filter",
		"--lessfly_dir="+this.Cfg.LessFlyDir,
		"--user="+sess.Uname,
		"--file="+userpath+"/.npmrc").Output(); err != nil {
		// TODO
	}

	rsp.Data.User = sess.Uname
	rsp.Data.BaseDir = userpath
	rsp.Status = 200
	rsp.Message = "OK"
}

func makedir(path string, uuid, ugid int, mode os.FileMode) error {

	if _, err := os.Stat(path); os.IsNotExist(err) {
		if err = os.MkdirAll(path, mode); err != nil {
			return err
		}
	} else {

		/* if stat.Mode() == 0777 {
		       fmt.Println("mode yes")
		   } else {
		       fmt.Println("mode no")
		   } */

		//fmt.Println(stat.Name(), stat.Mode(), mode, stat.IsDir())
	}

	if err := os.Chmod(path, mode); err != nil {
		return err
	}

	if err := os.Chown(path, uuid, ugid); err != nil {
		return err
	}

	return nil
}
