local redis = require "resty.redis"
local red = redis.new()
red.connect(red, '127.0.0.1', '6379')
myIP = ngx.var.remote_addr
local hasIP = red:sismember('black.ip',myIP)
if hasIP==1 then
        --ngx.say("This is 'Black List' request")
        ngx.exit(ngx.HTTP_FORBIDDEN)
end