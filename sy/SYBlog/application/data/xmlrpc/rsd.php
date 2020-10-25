<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
    <service>
        <engineName>SYBlog</engineName>
        <engineLink>http://blog.sylingd.com</engineLink>
        <homePageLink><?=$homeUrl?></homePageLink>
        <apis>
            <api name="WordPress" blogID="1" preferred="true" apiLink="<?=$wpXmlrpcUrl?>" />
            <api name="Movable Type" blogID="1" preferred="false" apiLink="" />
            <api name="MetaWeblog" blogID="1" preferred="false" apiLink="" />
            <api name="Blogger" blogID="1" preferred="false" apiLink="" />
        </apis>
    </service>
</rsd>';