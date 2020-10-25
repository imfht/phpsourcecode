//
//  PostViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class PostViewController:ViewController,UITableViewDelegate,UITableViewDataSource {
    var refreshControl = UIRefreshControl()
    //var flag = 2
    var resource:String!
    var navbarTitle:String!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.navigationItem.title = "\(navbarTitle)"
        
        var frame = self.view.frame
        self.tableView = UITableView(frame: frame, style:UITableViewStyle.Plain)
        
        self.tableView!.delegate = self
        self.tableView!.dataSource = self
        self.tableView!.registerClass(UITableViewCell.self, forCellReuseIdentifier: "PostCell")
        self.view.addSubview(self.tableView!)
        
        
        
        //添加刷新
        refreshControl.addTarget(self, action: "refreshData", forControlEvents: UIControlEvents.ValueChanged)
        refreshControl.attributedTitle = NSAttributedString(string: "松开后自动刷新")
        
        
        self.tableView!.addSubview(refreshControl)
        
        self.getData()
        
    }
    func getData(){
        
        var dl=HttpClient()
        //var url = "\(Constant().URL_POST_LIST)"
        var formatter:NSDateFormatter = NSDateFormatter()
        formatter.dateFormat = "yyyyMMddHHmmss"
        var now:String = formatter.stringFromDate(NSDate())
        var url = "\(Constant().URL_BASE_PATH)\(resource)?updatetime=\(now)"
        
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                
                var postArrayDic = dict?.objectForKey("_embedded") as NSDictionary
                self.dataArray = postArrayDic.objectForKey("items") as NSArray
                
                self.tableView?.reloadData()
                
            }
        })
    }
    
    // 刷新数据
    func refreshData() {
        self.getData()
        self.tableView!.reloadData()
        self.refreshControl.endRefreshing()
        
    }
    
    // MARK: - UITableViewDataSource
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return dataArray.count;
    }
    
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        
        let cell = UITableViewCell(style: UITableViewCellStyle.Subtitle, reuseIdentifier: "PostCell")
        
        //let obj: New = dataArray[indexPath.row] as New
        let obj:NSDictionary = dataArray[indexPath.row] as NSDictionary
        
        cell.textLabel?.text = obj.objectForKey("title") as? String
        
        
        //        let dateFormatter = NSDateFormatter()
        //        dateFormatter.dateFormat = "yyyy年 MM月 dd日"
        //
        //        let str = dateFormatter.stringFromDate(obj.title)
        
        var membername:String = obj.objectForKey("memberName") as String
        var createdDate:String = obj.objectForKey("created") as String
        var count:NSInteger = obj.objectForKey("comment_count") as NSInteger
        var lastCommentTime:String = obj.objectForKey("last_comment_time") as String
        
        var detailString:String = "\(membername)   回复:(\(count))   时间：\(lastCommentTime)"
        
        cell.detailTextLabel?.text = detailString
        
        var imgURL:String = obj.objectForKey("memberimageurl") as String
        var url = "\(Constant().URL_BASE_PATH)\(imgURL)"
        var nsd = NSData(contentsOfURL:NSURL(string:url))
        
        var img = UIImage(data: nsd);
        
        cell.imageView?.image = img
        
        return cell;
    }
    
    func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath){
        //        下面是取消点击后的选中
        //        tableView.deselectRowAtIndexPath(indexPath, animated: true)
        var postDetailVC = PostDetailViewController()
        let obj:NSDictionary = dataArray[indexPath.row] as NSDictionary
        
        postDetailVC.post = Post(dic: self.dataArray[indexPath.row] as NSDictionary)
        self.navigationController?.pushViewController(postDetailVC , animated: true)
        
    }
    
    
}