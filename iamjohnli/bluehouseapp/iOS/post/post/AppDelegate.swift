//
//  AppDelegate.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import UIKit

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {
    
    var window: UIWindow?
    
    
    func application(application: UIApplication, didFinishLaunchingWithOptions launchOptions: [NSObject: AnyObject]?) -> Bool {
        // Override point for customization after application launch.
        self.window = UIWindow(frame: UIScreen.mainScreen().bounds)
        self.window!.backgroundColor = UIColor.whiteColor()
        
        //        let postVC = PostViewController()
        //        let nav1 = UINavigationController(rootViewController: postVC)
        //        //let image1 = UIImage(named: "user_tabbar.png");
        //        nav1.tabBarItem = UITabBarItem(title: "论坛列表", image: nil,tag:1)
        //
        //
        //        let userVC = UserViewController()
        //        let nav2 = UINavigationController(rootViewController: userVC)
        //        //let image2 = UIImage(named: "comment_tabbar.png");
        //        nav2.tabBarItem = UITabBarItem(title: "用户信息", image: nil,tag:2)
        
        let nodeVC = NodeViewController()
        let nav1 = UINavigationController(rootViewController: nodeVC)
        //let image1 = UIImage(named: "user_tabbar.png");
        nav1.tabBarItem = UITabBarItem(title: "节点列表", image: nil,tag:1)
        
        let navArr = [nav1]
        
        //        let addPostVC = AddPostViewController()
        //        let nav3 = UINavigationController(rootViewController: addPostVC)
        //        //let image2 = UIImage(named: "comment_tabbar.png");
        //        nav3.tabBarItem = UITabBarItem(title: "添加帖子", image: nil,tag:3)
        //
        //        let navArr = [nav1,nav2,nav3]
        let tabBarController = UITabBarController()
        tabBarController.viewControllers = navArr
        self.window!.rootViewController = tabBarController
        
        self.window!.makeKeyAndVisible()
        return true
    }
    
    func applicationWillResignActive(application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
    }
    
    func applicationDidEnterBackground(application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
    }
    
    func applicationWillEnterForeground(application: UIApplication) {
        // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
    }
    
    func applicationDidBecomeActive(application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    }
    
    func applicationWillTerminate(application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
    }
    
    
}

