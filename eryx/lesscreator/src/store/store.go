package store

import (
	"../../deps/lessgo/service/lesskeeper"
	"../conf"
	"errors"
	"sync"
)

var (
	locker sync.Mutex
	conns  map[string]*lesskeeper.Client
	err    error
	Keeper lesskeeper.Client
)

func Initialize() error {

	conns = map[string]*lesskeeper.Client{}

	Keeper, err = lesskeeper.NewClient(
		conf.Config.LessKeeper.ApiUrl,
		conf.Config.LessKeeper.Bucket,
		conf.Config.LessKeeper.AccessKey,
		conf.Config.LessKeeper.SecretKey)

	return err
}

func Register(key string, cfg lesskeeper.Client) (*lesskeeper.Client, error) {

	locker.Lock()
	defer locker.Unlock()

	if c, ok := conns[key]; ok {
		if c.ApiUrl == cfg.ApiUrl &&
			c.Bucket == cfg.Bucket &&
			c.AccessKey == cfg.AccessKey &&
			c.SecretKey == cfg.SecretKey {
			return c, nil
		}
	}

	c, err := lesskeeper.NewClient(
		cfg.ApiUrl,
		cfg.Bucket,
		cfg.AccessKey,
		cfg.SecretKey)

	// c.Timeout(10)

	if err == nil {
		conns[key] = &c
	}

	return &c, err
}

func Pull(key string) (*lesskeeper.Client, error) {

	c, ok := conns[key]
	if ok {
		return c, nil
	}

	return c, errors.New("---")
}
