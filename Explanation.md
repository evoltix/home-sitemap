------------------------------------------------
=>The problem to be solved in your own words
------------------------------------------------
This task is a very good mental exercise. The main requirement looks very simple a { sitemap for the home page}.
But it's not that much simple :). Ok so how I started working for this task.

1. I wrote a simple program in PHP(No plugin at that time) to extract the sitemap of the home page of any website.
2. I used the DOM Object to parse the home page content.
3. Then I extracted internal and external links in an array.
4. Finally, I created a plugin in WordPress and integrate that code into WordPress plugin.

---------------------------------------------------------------------------------------------------
=>A technical spec of how you will solve it. Extracting all the internal links was not a big task. 
------------------------------------------------------------------------------------------------
But the real problem while extracting the internal link was the variety of internal links.

-----------
   Hurdle One: Below is a list of internal links which may occur while extracting:
-----------
<ul>
  <li> 1. http://www.website.com/page
  <li> 2. http://www.website.com/?page_id={num}
  <li> 3. ?page_id={num}
  <li> 4. http://www.website.com/#anchor
  <li> 5. #anchor
  <li> 6. Duplicate internal links So we have to use multiple regular expressions to filter internal links.
</ul>
<b>    Hurdle Two:Any internal link on the home page might have any name like <a href="http://www.website.com/page">Read More</a> but we can not save the internal link like that.
</b>
For that, we have to get the UID of a page using WordPress using url_to_postid function once we get the uid of URL we can easily
get the title of the Internal link and now link will look like<a href="http://www.website.com/page">Page Title</a> 

-----------
   Hurdle Three:Storing the Data into the database:
-----------

I used transient to store the sitemap data because we need the data temporarily stored and data will update in every 1 hr.

-----------
   Hurdle Four:Scheduling Action to generate a sitemap in every hr and store data into transient again
-----------
I used wp_schedule_event and wp_next_scheduled with my custom action and it worked like a charm
Hurdle five:phpcs  Frankly speaking, I never used this tool before so still, my code is full of error and warning as per phpcs rules. :( These 5 hurdles took most of my time while I was developing the plugin rest of the part was much easier.

-----------
   How the code itself works and why and How your solution achieves the admin’s desired outcome per the user story:
-----------

<b>Step1</b>. Plugin code first checks the home page links using the DOM object. Dom object is the best way to parse HTMLStep2. Then regular expression and if, else condition is used to filter the Internal links and external linksStep3. Fetch the internal link Title and I used built-in WordPress functions for that.Step4. Store the generated sitemap in Database using transientStep5. Generate the sitemap.html file and store it in root folderStep6. Regenerate the data in every one hr and follow the step1 to step5Step7. Fetch the sitemap from the database when admin require it using get_transient functionStep8. Show the instruction to display the sitemap in the front end.
Note* generate_home_sitemap is an action hook to generate, store and schedule the sitemap           show_sitemap_admin is an action hook to view the sitemap in the admin area          hs_admin_response  is an action hook to render the notice and output in the admin area          hs_add_cron_interval is a filter to control the schedule time interval


 





The technical decisions you made and why
Option1: I can use a simple module in CakePHP and then connect it with WordPress website using WordPress API
orOption2: I can create a simple WordPress plugin that can run on any WordPress website.
I choose option 2 because I can use any WordPress library if needed.



Regards,Avnish NegiFull Stack Web Developer+91-9582577578
