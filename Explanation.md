<p><strong> =>The problem to be solved in your own words</strong> </p>
<p>This task is a very good mental exercise. The main requirement looks very simple a { sitemap for the home page}.
  But it's not that much simple :).</p>
<p> Ok so how I started working for this task.  </p>
<p>1. I wrote a simple program in PHP(No plugin at that time) to extract the sitemap of the home page of any website.</p>
<p> 2. I used the DOM Object to parse the home page content.</p>
<p> 3. Then I extracted internal and external links in an array.</p>
<p> 4. Finally, I created a plugin in WordPress and integrate that code into WordPress plugin.</p>
<p>&nbsp;</p>
<p><strong> =>A technical spec of how you will solve it</strong>.</p>
<p> Extracting all the internal links was not a big task.
  But the real problem while extracting the internal link was the variety of internal links.</p>
<p><strong> Hurdle One: Below is a list of internal links which may occur while extracting: </strong></p>
<ul>
  <li> 1. http://www.website.com/page
  <li> 2. http://www.website.com/?page_id={num}
  <li> 3. ?page_id={num}
  <li> 4. http://www.website.com/#anchor
  <li> 5. #anchor
  <li> 6. Duplicate internal links So we have to use multiple regular expressions to filter internal links.
</ul>
<p><b> Hurdle Two: Any internal link on the home page might have any name like <a href="http://www.website.com/page">Read More</a> but we can not save the internal link like that.</b> </p>
<p>For that, we have to get the UID of a page using WordPress using <strong>url_to_postid</strong> function once we get the uid of URL we can easily
  get the title of the Internal link and now link will look like <a href="http://www.website.com/page">Page Title</a> </p>
<p><strong> Hurdle Three:Storing the Data into the database:</strong></p>
<p>  I used <strong>transient</strong> to store the sitemap data because we need the data temporarily stored and data will update in every 1 hr.  </p>
<p><strong>Hurdle Four: Scheduling Action to generate a sitemap in every hr and store data into transient again</strong></p>
<p> I used wp_schedule_event and wp_next_scheduled with my custom action and it worked like a charm.</p>
<p><strong> Hurdle five:phpcs</strong></p>
<p>  Frankly speaking, I never used this tool before so still, my code is full of error and warning as per phpcs rules. :</p>
<p>( These 5 hurdles took most of my time while I was developing the plugin rest of the part was much easier.</p>
<p>&nbsp;</p>
<p><strong>=>How the code itself works and why and How your solution achieves the admin’s desired outcome per the user story:</strong></p>
<p><b>Step1</b>. Plugin code first checks the home page links using the DOM object. Dom object is the best way to parse HTML.</p>
<p><strong>Step2.</strong> Then regular expression and if, else condition is used to filter the Internal links and external links.</p>
<p><strong>Step3.</strong> Fetch the internal link Title and I used built-in WordPress functions for that.</p>
<p><strong>Step4.</strong> Store the generated sitemap in Database using transient</p>
<p><strong>Step5.</strong> Generate the sitemap.html file and store it in root folder</p>
<p><strong>Step6.</strong> Regenerate the data in every one hr and follow the step1 to step5</p>
<p><strong>Step7.</strong> Fetch the sitemap from the database when admin require it using <strong>get_transient</strong> function</p>
<p><strong>Step8.</strong> Show the instruction to display the sitemap in the front end.  </p>
<p><strong>Actions and Filters:</strong></p>
<p><strong>generate_home_sitemap is an action hook to generate, store and schedule the sitemap.  </strong></p>
<p><strong>show_sitemap_admin is an action hook to view the sitemap in the admin area</strong>.</p>
<p><strong> hs_admin_response  is an action hook to render the notice and output in the admin area</strong>.</p>
<p><strong> hs_add_cron_interval is a filter to control the schedule time interval</strong>.</p>
<p class="style1">&nbsp;</p>
<p><strong>=>The technical decisions you made and why:</strong></p>
<p> <strong>Option1</strong>: I can use a simple module in CakePHP and then connect it with WordPress website using WordPress API  </p>
<p>or</p>
<p><strong>Option2:</strong> I can create a simple WordPress plugin that can run on any WordPress website.
  I choose option 2 because I can use any WordPress library if needed.  </p>
