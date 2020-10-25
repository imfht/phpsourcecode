package utils

import (
	"encoding/json"
)

func JsonDecode(str string, rs interface{}) (err error) {

	if err = json.Unmarshal([]byte(str), &rs); err != nil {
		return err
	}

	return nil
}

func JsonEncode(rs interface{}) (str string, err error) {
	rb, err := json.Marshal(rs)
	if err == nil {
		str = string(rb)
	}
	return
}
