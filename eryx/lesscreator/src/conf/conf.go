package conf

import (
	"../../deps/lessgo/service/lessids"
	"../../deps/lessgo/service/lesskeeper"
	"../../deps/lessgo/utils"
	"errors"
	"fmt"
	"io/ioutil"
	"os"
	"path"
	"path/filepath"
	"regexp"
	"strings"
)

const VERSION string = "1.0.0"

var (
	Config ConfigCommon
)

type ConfigCommon struct {
	Version    string
	Prefix     string
	RunUser    string
	ApiPort    string            `json:"apiport"`
	LessFlyApi string            `json:"lessfly_api"`
	LessIdsUrl string            `json:"lessids_url"`
	LessKeeper lesskeeper.Client `json:"lesskeeper"`
	LessFlyDir string            `json:"lessfly_dir"`
}

func Initialize(prefix string) error {

	if prefix == "" {
		if p, err := filepath.Abs(os.Args[0]); err == nil {
			p, _ = path.Split(p)
			prefix, _ = filepath.Abs(p + "/..")
		}
	}

	reg, _ := regexp.Compile("/+")
	prefix = "/" + strings.Trim(reg.ReplaceAllString(prefix, "/"), "/")

	cfgfile := prefix + "/etc/creator.json"
	if _, err := os.Stat(cfgfile); err != nil && os.IsNotExist(err) {
		return errors.New("Error: config cfgfile is not exists")
	}

	fp, err := os.Open(cfgfile)
	if err != nil {
		return errors.New(fmt.Sprintf("Error: Can not open (%s)", cfgfile))
	}
	defer fp.Close()

	cfgstr, err := ioutil.ReadAll(fp)
	if err != nil {
		return errors.New(fmt.Sprintf("Error: Can not read (%s)", cfgfile))
	}

	if err = utils.JsonDecode(cfgstr, &Config); err != nil {
		return errors.New(fmt.Sprintf("Error: "+
			"config file invalid. (%s)", err.Error()))
	}

	Config.Version = VERSION
	Config.Prefix = prefix

	lessids.ServiceUrl = Config.LessIdsUrl

	return nil
}
