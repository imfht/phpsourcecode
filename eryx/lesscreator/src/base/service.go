package base

const (
	ServiceApiVersion = "1.0.0"
)

type ServiceResponse struct {
	Status     int    `json:"status"`
	Message    string `json:"message,omitempty"`
	ApiVersion string `json:"apiVersion,omitempty"`
}
