//
//  HttpClient.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation

class HttpClient:NSObject,NSURLConnectionDataDelegate{
    let SYMBOL:NSString?="AaB03x" //分界线的标识符 @"AaB03x"
    var connection:NSURLConnection?
    //接受数据的变量
    var receiveData:NSMutableData?
    //上传二进制数据的数据格式如jpg、mp3
    var contentType:String!
    //回调的闭包
    var completeBlock:((data:NSData?,error:NSError?)->Void)?
    
    override init() {
        super.init()
        receiveData=NSMutableData()
    }
    deinit{
        
    }
    func cancel(){
        if (connection != nil){
            connection!.cancel()
        }
    }
    //get 请求
    func downloadFromGetUrl(url:NSString, completionHandler:((data:NSData?,error:NSError?)->Void)){
        
        
        url.stringByAddingPercentEscapesUsingEncoding(NSUTF8StringEncoding)
        let newUrl = NSURL(string: url)
        
        let request=NSMutableURLRequest(URL: newUrl)
        
        
        
        //设置HTTPHeader
        //[request setValue:content forHTTPHeaderField:@"Content-Type"];
        //        request.addValue("HTTP_USER_AGENT iOS", forHTTPHeaderField:"WH-Context")
        //
        //        println(request.allHTTPHeaderFields)
        
        
        
        
        
        
        connection=NSURLConnection(request: request, delegate:self)
        
        self.completeBlock=completionHandler
    }
    //post 请求(dic里没有NSData )
    func downloadFromPostUrl(url:NSString,dic:NSDictionary, completionHandler:((data:NSData?,error:NSError?)->Void)){
        
        url.stringByAddingPercentEscapesUsingEncoding(NSUTF8StringEncoding)
        let newUrl = NSURL(string: url)
        
        let request=NSMutableURLRequest(URL:newUrl)
        request.timeoutInterval=10.0
        request.HTTPMethod="POST"
        var param=NSMutableArray()
        for key:AnyObject in dic.allKeys{
            var s=NSString(format:"\(key as NSString)=\(dic[key as NSString])")
            param.addObject(s)
        }
        var bodyString=param.componentsJoinedByString("&") as NSString
        request.HTTPBody=bodyString.dataUsingEncoding(NSUTF8StringEncoding)
        connection=NSURLConnection(request: request, delegate:self)
        self.completeBlock=completionHandler
    }
    //post 请求(dic里包含NSData)需要设置 contentType的类型
    func downloadNSDataFromPostUrl(url:NSString,dic:NSDictionary, completionHandler:((data:NSData?,error:NSError?)->Void)){
        
        url.stringByAddingPercentEscapesUsingEncoding(NSUTF8StringEncoding)
        let newUrl = NSURL(string: url)
        
        let request=NSMutableURLRequest(URL:newUrl)
        request.timeoutInterval=10.0
        request.HTTPMethod="POST"
        
        let start=NSString(format:"--\(SYMBOL)")
        let end=NSString(format:"--\(SYMBOL)--")
        var bodyString=NSMutableString()
        var dataKey:NSString?
        for key : AnyObject in dic.allKeys{
            var value : AnyObject!=dic[key as NSString]
            if value.isKindOfClass(NSData){
                dataKey=NSString(format:"\(key)")
            }else{
                bodyString.appendFormat("\(start)\r\n")
                //添加字段名称，换2行
                bodyString.appendFormat("Content-Disposition: form-data; name=\"\(key)\"\r\n\r\n")
                //添加字段的值
                bodyString.appendFormat("\(dic[key as NSString])\r\n")
            }
        }
        //添加分界线，换行
        bodyString.appendFormat("\(start)\r\n")
        
        //声明pic字段，文件名为boris.png
        bodyString.appendFormat("Content-Disposition: form-data; name=\"\(dataKey)\"; filename=\"\(dataKey).\(contentType)\"\r\n")
        
        //声明上传文件的格式
        bodyString.appendFormat("Content-Type: \(contentType)\r\n\r\n")
        
        //声明结束符：--AaB03x--
        var endStr=NSString(format:"\r\n\(end)")
        
        //声明myRequestData，用来放入http body
        var myRequestData=NSMutableData()
        //将body字符串转化为UTF8格式的二进制
        myRequestData.appendData(bodyString.dataUsingEncoding(NSUTF8StringEncoding)!)
        //将image的data加入
        myRequestData.appendData(dic[dataKey!] as NSData);
        //加入结束符--AaB03x--
        myRequestData.appendData(endStr.dataUsingEncoding(NSUTF8StringEncoding)!);
        
        //设置HTTPHeader中Content-Type的值
        var  content=NSString(format:"multipart/form-data; boundary=\(SYMBOL)")
        //设置HTTPHeader
        //[request setValue:content forHTTPHeaderField:@"Content-Type"];
        request.addValue(content, forHTTPHeaderField:"Content-Type")
        //设置Content-Length
        request.addValue(String(myRequestData.length), forHTTPHeaderField:"Content-Length")
        
        request.HTTPBody=myRequestData
        connection=NSURLConnection(request: request, delegate:self)
        self.completeBlock=completionHandler
    }
    //NSURLConnectionDataDelegate
    func connection(connection: NSURLConnection!, didReceiveResponse response: NSURLResponse!){
        var newResponse=response as NSHTTPURLResponse
        println("statusCode=\(newResponse.statusCode)")
        
    }
    
    func connection(connection: NSURLConnection!, didReceiveData data: NSData!){
        receiveData!.appendData(data)
    }
    
    func connectionDidFinishLoading(connection: NSURLConnection!){
        if (completeBlock != nil){
            completeBlock!(data:receiveData,error:nil)
        }
        
    }
    func connection(connection: NSURLConnection!, didFailWithError error: NSError!){
        if (completeBlock != nil){
            completeBlock!(data:receiveData,error:error)
        }
        
    }
    
}