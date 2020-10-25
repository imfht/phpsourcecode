
package com.ufqi.gwa2;

import java.io.IOException;
import java.util.Arrays;

import com.ufqi.gwa2.inc.WebApp;
import com.ufqi.gwa2.mod.User;
import com.ufqi.gwa2.ctrl.*;

public class Index{
    

    public static void main(String args[]) throws IOException{

        User user = new User();
        System.out.println("GWA2Java application entry.....\nUser is initing...."+(user)+"\n");
        System.out.println("Try to load ctrl/controller: "+(Arrays.toString(args))+"\n");

    }

}
