
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Feed2JS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 700px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
    </style>
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="">Home</a></li>
          <li><a href="build.php">Build</a></li>
          <li><a href="#about">About</a></li>
        </ul>
        <h3 class="muted">Feed2JS</h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Embed RSS Feeds in 3 Simple Steps</h1>
        <p class="lead"><strong>Build</strong> the feed, <strong>Generate</strong> the code, and <strong>Style</strong> the results.</p>
        <a class="btn btn-large btn-success" href="build.php">Get Started</a>
      </div>

      <hr>

      <div class="row-fluid marketing">
      	<h3 id="about">About Feed2JS</h3>
      	<p>This PHP script will take an RSS feed as a value of src="...." and return a JavaScript file that can be linked remotely from any other web page. Output includes site title, link, and description as well as item site, link, and description with these outouts contolled by extra parameters.</p>
      </div>

      <hr>

      <div class="footer">
        <p><strong>Feed2JS v2.32</strong></p>
		<p>Feed2JS code is Copyright (C) 2004-<?php echo date("Y")?>  Created by <a href="http://cogdogblog.com/">Alan Levine</a>. Source code available on <a href="https://github.com/cogdog/feed2js">GitHub</a>.</p>
		<p>This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.</p>
		<p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details <a href="http://www.gnu.org/licenses/gpl.html">http://www.gnu.org/licenses/gpl.html</a></p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  </body>
</html>