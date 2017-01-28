<?php



class rss extends controller
{
    public static $page;

    function __construct ()
    {
        self::$page=(router::get('page',1))?:1;
    }

    function indexAction()
    {
        /*
        <?xml version="1.0" encoding="utf-8"?>
        <rss version="2.0">
        <channel>
        <title>Title of your Feed</title>
        <link>http://www.yourwebsite.com/</link>
        <description>This is the description of your Feed.
        Keep it to one or two sentences.</description>
        */

        /*
        <item>
        <title>Content Title</title>
        <link>Direct URL to content</link>
        <guid>Unique ID for content. Copy the URL again</guid>
        <pubDate>Wed, 27 Nov 2013 15:17:32 GMT
        (Note: The date must be in this format)</pubDate>
        <description>Description for your content.</description>
        </item>
        */

        /*
        <?xml version="1.0" encoding="utf-8"?>
        <rss version="2.0">
        <channel>
        <title>My Cool Blog</title>
        <link>http://www.yourwebsite.com/</link>
        <description>My latest cool articles</description>
        <item>
        <title>Article 3</title>
        <link>example.com/3</link>
        <guid>example.com/3</guid>
        <pubDate>Wed, 27 Nov 2013 13:20:00 GMT</pubDate>
        <description>My newest article.</description>
        </item>
        <item>
        <title>Article 2</title>
        <link>example.com/2</link>
        <guid>example.com/2</guid>
        <pubDate>Tue, 26 Nov 2013 12:15:12 GMT</pubDate>
        <description>My second article.</description>
        </item>
        <item>
        <title>Article 1</title>
        <link>example.com/1</link>
        <guid> example.com/1</guid>
        <pubDate>Mon, 25 Nov 2013 15:10:45 GMT</pubDate>
        <description>My first article.</description>
        </item>
        </channel>
        </rss>
        */
    }

}
