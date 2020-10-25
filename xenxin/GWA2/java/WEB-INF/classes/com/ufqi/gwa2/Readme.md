
# Use GWA2Java in cmdline or JavaBean

## GWA2Java cmdline

    ### compose GWA2 in .java files

    ### compile them into .class files

    cd ./WEB-INF/classes

    java com.ufqi.gwa2.Index "?mod=user"


## GWA2Java in .jsp

    ### compose GWA2 in .java files

    ### compile them into .class files

    ### import and use in .jsp:
    
    #### as of a single class
    <@page import="com.ufqi.gwa2.mod.User"%><%
        com.ufqi.gwa2.mod.User modUser = new com.ufqi.gwa2.mod.User();
        modUser.getId();
        //....
    %>

    #### as of a JavaBean
    <jsp:useBean id="modUser" class="com.ufqi.gwa2.mod.User" scope="application">
        <jsp:setProperty name="modUser" property="param1" value="value1" />
    </jsp:useBean>
    <%
        modUser.getId();
    %>

## Why Tomcat cannot compile automatically .java files under ./WEB-INF?
    
    ## as of Caucho Resin?
